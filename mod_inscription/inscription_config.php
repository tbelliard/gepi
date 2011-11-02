<?php
/*
 * $Id: inscription_config.php 6608 2011-03-03 14:50:57Z crob $
 *
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

include("../fckeditor/fckeditor.php") ;



// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ../logout.php?auto=1");
   die();
}

// Check access
if (!checkAccess()) {
   header("Location: ../logout.php?auto=1");
   die();
}

if (!isset($_SESSION['order_by_'])) {$_SESSION['order_by_'] = "date";}
$_SESSION['order_by_'] = isset($_POST['order_by_']) ? $_POST['order_by_'] : (isset($_GET['order_by_']) ? $_GET['order_by_'] : $_SESSION['order_by_']);
$order_by_ = $_SESSION['order_by_'];
$id_inter = isset($_POST['id_inter']) ? $_POST['id_inter'] : (isset($_GET['id_inter']) ? $_GET['id_inter'] : NULL);


// Suppression d'un item
if (isset($_GET['action']) and ($_GET['action'] == "supprimer")) {
    $del = mysql_query("delete from inscription_j_login_items where id='".$_GET['id_inter']."'");
    $del = mysql_query("delete from inscription_items where id='".$_GET['id_inter']."'");
    $msg = "Les modifications ont été enregistrées.";
}

// Enregistrement


if (isset($_POST['is_posted_notes'])) {
  check_token();

  $msg = "";
  if (!saveSetting("active_inscription_utilisateurs", $_POST['activer']))
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	if (isset($NON_PROTECT['notes'])) {
    $msg = "";
    $imp = traitement_magic_quotes($NON_PROTECT['notes']);
    if (!saveSetting("mod_inscription_explication", $imp)) $msg .= "Erreur lors de l'enregistrement du paramètre mod_inscription_explication !";
  }
  if (!saveSetting("mod_inscription_titre", $_POST['mod_inscription_titre']))
    $msg .= "Erreur lors de l'enregistrement de mod_inscription_titre !";
}
if (isset($_POST['is_posted'])) {
    check_token();

    $msg = "";
    if ($_POST['is_posted'] == "ajout") {
        $req = mysql_query("insert into inscription_items set
        date='".$_POST['date']."',
        heure='".$_POST['heure']."',
        description='".$_POST['description']."'
        ");
    } else {
        $req = mysql_query("update inscription_items set
        date='".$_POST['date']."',
        heure='".$_POST['heure']."',
        description='".$_POST['description']."'
        where id = '".$_POST['id_inter']."'
        ");
    }
    $msg .= "Les modifications ont été enregistrées.";
}

$call_data = mysql_query("SELECT * FROM inscription_items ORDER BY $order_by_");
$nombre_lignes = mysql_num_rows($call_data);

if (!loadSettings()) {
    die("Erreur chargement settings");
}
//**************** EN-TETE *****************
$titre_page = "Configuration du module Inscription";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************


// Ajout d'un item
if (isset($_GET['action']) and ($_GET['action'] == "ajout")) {
    echo "<p class=bold><a href=\"./inscription_config.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href=\"./inscription_config.php?action=ajout\">Ajouter un item</a> | <a href=\"javascript:centrerpopup('help.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">Aide</a></p>\n";

    echo "<form name=\"formulaire\" method=\"post\" action=\"inscription_config.php\">";

	echo add_token_field();

    if (isset($id_inter)) {
        $req = mysql_query("select * from inscription_items where id='".$id_inter."'");
        $date = htmlentities(@mysql_result($req, 0, "date"));
        $heure = htmlentities(@mysql_result($req, 0, "heure"));
        $description = htmlentities(@mysql_result($req, 0, "description"));
        echo "<input type=\"hidden\" name=\"is_posted\" value=\"modif\" />\n";
        echo "<input type=\"hidden\" name=\"id_inter\" value=\"".$id_inter."\" />\n";
    } else {
        $date = "";
        $heure = "";
        $description = "";
        echo "<input type=\"hidden\" name=\"is_posted\" value=\"ajout\" />\n";
    }
    echo "<H2>Ajout d'un item</H2>\n";
    echo "<p>un item correspond à une entité (stage, intervention dans les établissements, réunion...) à laquelle les utilisateurs peuvent s'inscrire.</p>\n";
    echo "<table cellpadding=\"6\">\n";
    echo "<tr><td>Date (au format AAAA/MM/JJ) : </td><td><input type=\"text\" name=\"date\" value=\"$date\" size=\"20\" /></td></tr>\n";
    echo "<tr><td>Heure : </td><td><input type=\"text\" name=\"heure\" value=\"$heure\" size=\"20\" /></td></tr>\n";
    echo "<tr><td>Description (lieu, ...) : </td><td><input type=\"text\" name=\"description\" value=\"$description\" size=\"50\" /></td></tr>\n";

    echo "</table>\n";
    echo "<input type=\"submit\" name=\"Enregistrer\" value=\"Envoyer\" />\n";
    echo "</form>\n";
    require("../lib/footer.inc.php");
    die();
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href=\"./inscription_config.php?action=ajout\">Ajouter un item</a> | <a href=\"javascript:centrerpopup('help.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">Aide</a></p>\n";
echo "<form name=\"formulaire\" method=\"post\"  action=\"inscription_config.php\">\n";
echo add_token_field();
echo "<H2>Activation  / Désactivation</H2>\n";
$active_prof = getSettingValue("active_inscription_utilisateurs");
if ($active_prof == "y") {
  echo "Actuellement, la page autorisant les inscriptions est activée : les utilisateurs (professeurs, cpe, scolarité) peuvent donc s'inscrire.";
} else {
  echo "Actuellement, la page autorisant les inscriptions n'est pas activée : les utilisateurs (professeurs, cpe, scolarité) ne peuvent pas s'inscrire.";
}
?><br /> <label for='activer_y' style='cursor: pointer;'><input type="radio" name="activer" id="activer_y" value="y" <?php if (getSettingValue("active_inscription_utilisateurs")=='y') echo " checked"; ?> />
&nbsp;Activer l'acc&egrave;s aux inscriptions</label><br />
<label for='activer_n' style='cursor: pointer;'><input type="radio" name="activer" id="activer_n" value="n" <?php if (getSettingValue("active_inscription_utilisateurs")=='n') echo " checked"; ?> />
&nbsp;Désactiver l'accès aux inscriptions</label>
<?php
echo "<H2>Liste des items</H2>\n";
if ($nombre_lignes != 0) {
  echo "<p>Chaque item ci-dessous correspond à une entité (stage, intervention dans les établissements, réunion...) à laquelle les utilisateurs peuvent s'inscrire.</p>\n";
  echo "<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\">\n";
  echo "<tr>\n";
  echo "<td><p class='bold'><a href='inscription_config.php?order_by_=id'>N°</a></p></td>\n";
  echo "<td><p class='bold'><a href='inscription_config.php?order_by_=date'>Date</a></p></td>\n";
  echo "<td><p class='bold'>Heure</p></td>\n";
  echo "<td><p class='bold'><a href='inscription_config.php?order_by_=description'>Description (lieu, ...)</a></p></td>\n";
  echo "<td><p class='bold'>Personnes actuellement inscrites</p></td>\n";
  echo "<td><p class='bold'>-</p></td>\n";
  echo "</tr>\n";
  $_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
  $i = 0;
  while ($i < $nombre_lignes){
    $id = mysql_result($call_data, $i, "id");
    $date = mysql_result($call_data, $i, "date");
    $heure = mysql_result($call_data, $i, "heure");
    $description = mysql_result($call_data, $i, "description");

    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);
    $date = mktime(0,0,0,$month,$day,$year);
    $date = strftime("%A %d %B %Y", $date);


    $inscrit = sql_query1("select id from inscription_j_login_items
    where login='".$_SESSION['login']."' and id='".$id."' ");

    $inscrits = mysql_query("select login from inscription_j_login_items
    where id='".$id."' ");
    $nb_inscrits = mysql_num_rows($inscrits);
    if ($nb_inscrits == 0) $noms_inscrits = "<center>-</center>"; else $noms_inscrits = "";
    $k = 0;
    while ($k < $nb_inscrits) {
        $login_inscrit = mysql_result($inscrits, $k, "login");
        $nom_inscrit = sql_query1("select nom from utilisateurs where login='".$login_inscrit."'");
        if ($nom_inscrit == -1) $nom_inscrit = "<font color='red'>(Nom absent => login : ".$login_inscrit.")</font>";
        $prenom_inscrit = sql_query1("select prenom from utilisateurs where login='".$login_inscrit."'");
        if ($prenom_inscrit == -1) $prenom_inscrit = "";
        $noms_inscrits .=$prenom_inscrit." ".$nom_inscrit."<br />";
        $k++;
    }


    echo "<tr>\n";
    echo "<td>$id <br /><a href=\"./inscription_config.php?id_inter=$id&amp;action=ajout\" >Modifier</a></td>\n";
    echo "<td>$date</td>\n";
    echo "<td>$heure</td>\n";
    echo "<td>$description</td>\n";
    echo "<td>".$noms_inscrits."</td>\n";
    echo "<td><a href=\"./inscription_config.php?id_inter=$id&amp;action=supprimer\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir supprimer cet item ?')\">Supprimer</a></td>\n";
    echo "</tr>\n";
    $i++;
  }
  echo "</table>\n";
} else {
  echo "<p>Actuellement aucun item n'est présent dans la base.";
}
$contenu = getSettingValue("mod_inscription_explication");
echo "<H2>Titre du module</H2>\n";
echo "<input type=\"text\" name=\"mod_inscription_titre\" size=\"40\" value=\"".getSettingValue("mod_inscription_titre")."\" />\n";
echo "<H2>Texte explicatif</H2>\n";
echo "<p>Le texte ci-dessous sera visible par les personnes accédant au module d'inscription/désincription.</p>\n";
echo "<input type=\"hidden\" name=\"is_posted_notes\" value=\"yes\" />\n";
    // lancement de FCKeditor

    $oFCKeditor = new FCKeditor('no_anti_inject_notes') ;
    $oFCKeditor->BasePath = '../fckeditor/' ;
    $oFCKeditor->Config['DefaultLanguage']  = 'fr' ;
    $oFCKeditor->ToolbarSet = 'Basic' ;
    $oFCKeditor->Value = $contenu ;
    $oFCKeditor->Create() ;
    echo "<br /><br />&nbsp;";




echo "<div id=\"fixe\"><center>";
echo "<input type=\"submit\" name=\"ok\" value=\"Enregistrer\" />";
echo "</center></div>\n";
echo "</form>\n";
require("../lib/footer.inc.php");?>
