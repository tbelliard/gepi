<?php
/*
 * $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$action = isset($_POST["action"]) ? $_POST["action"] :(isset($_GET["action"]) ? $_GET["action"] :NULL);

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Dans le cas ou on poste un message, pas de traitement anti_inject
// Pour ne pas interférer avec fckeditor
if ((isset($action)) and ($action == 'message') and (isset($_POST['message'])) and isset($_POST['ok']))
    $traite_anti_inject = 'no';

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
include("../fckeditor/fckeditor.php") ;

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}
//Configuration du calendrier
include("../lib/calendrier/calendrier.class.php");
$cal1 = new Calendrier("formulaire", "display_date_debut");
$cal2 = new Calendrier("formulaire", "display_date_fin");

// initialisation de $id_mess
$id_mess = isset($_POST["id_mess"]) ? $_POST["id_mess"] :(isset($_GET["id_mess"]) ? $_GET["id_mess"] :NULL);

$order_by = isset($_POST["order_by"]) ? $_POST["order_by"] :(isset($_GET["order_by"]) ? $_GET["order_by"] :"date_debut");
if ($order_by != "date_debut" and $order_by != "date_fin" and $order_by != "id") {
	$order_by = "date_debut";
}

//
// Suppression d'un message
//
if ((isset($action)) and ($action == 'sup_entry')) {
   $res = sql_query("delete from messages where id = '".$_GET['id_del']."'");
   if ($res) $msg = "Suppression réussie";
}

//
// Annulation des modifs
//
if ((isset($action)) and ($action == 'message') and (isset($_POST['cancel']))) {
    unset ($id_mess);
}


//
// Insertion ou modification d'un message
//
if ((isset($action)) and ($action == 'message') and (isset($_POST['message'])) and isset($_POST['ok'])) {
    $record = 'yes';
    $contenu_cor = traitement_magic_quotes(corriger_caracteres($_POST['message']));
    $destinataires = '_';
    if (isset($_POST['desti_s'])) $destinataires .= 's';
    if (isset($_POST['desti_p'])) $destinataires .= 'p';
    if (isset($_POST['desti_c'])) $destinataires .= 'c';
    if (isset($_POST['desti_a'])) $destinataires .= 'a';
    if ($contenu_cor == '') {
        $msg = "ATTENTION : Le message est vide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';
    }
    if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date_debut'])) {
        $anneed = substr($_POST['display_date_debut'],6,4);
        $moisd = substr($_POST['display_date_debut'],3,2);
        $jourd = substr($_POST['display_date_debut'],0,2);
        while ((!checkdate($moisd, $jourd, $anneed)) and ($jourd > 0)) $jourd--;
        $date_debut=mktime(0,0,0,$moisd,$jourd,$anneed);
    } else {
        $msg = "ATTENTION : La date de début d'affichage n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';

    }
    if (ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $_POST['display_date_fin'])) {
        $anneef = substr($_POST['display_date_fin'],6,4);
        $moisf = substr($_POST['display_date_fin'],3,2);
        $jourf = substr($_POST['display_date_fin'],0,2);
        while ((!checkdate($moisf, $jourf, $anneef)) and ($jourf > 0)) $jourf--;
        $date_fin=mktime(0,0,0,$moisf,$jourf,$anneef);
    } else {
        $msg = "ATTENTION : La date de fin d'affichage n'est pas valide.<br />L'enregistrement ne peut avoir lieu.";
        $record = 'no';
    }
    if ($record == 'yes') {
      if (isset($_POST['id_mess'])) {
          $req = mysql_query("UPDATE messages
          SET texte = '".$contenu_cor."',
          date_debut = '".$date_debut."',
          date_fin = '".$date_fin."',
          auteur='".$_SESSION['login']."',
          destinataires = '".$destinataires."'
          WHERE (id ='".$_POST['id_mess']."')");
      } else {
          $req = mysql_query("INSERT INTO messages
          SET texte = '".$contenu_cor."',
          date_debut = '".$date_debut."',
          date_fin = '".$date_fin."',
          auteur='".$_SESSION['login']."',
          destinataires = '".$destinataires."'
          ");
      }
      if ($req) {
          $msg = "Enregistrement réussi.";
          unset($contenu_cor);
          unset($_POST['display_date_debut']);
          unset($_POST['display_date_fin']);
          unset($id_mess);
          unset($destinataires);
      } else {
          $msg = "Problème lors de l'enregistrement du message.";
      }
    }
}

$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Gestion des messages";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************
echo "<script type=\"text/javascript\" language=\"JavaScript\" SRC=\"../lib/clock_fr.js\"></SCRIPT>";
//-----------------------------------------------------------------------------------
echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
echo "<table width=\"98%\" cellspacing=0 align=\"center\">";
echo "<tr>";
echo "<td valign='top'>";
echo "<p>Nous sommes le :&nbsp;<br />";
echo "<script type=\"text/javascript\" language=\"javascript\">";
echo "<!--\n";
echo "new LiveClock();\n";
echo "//-->";
echo "</SCRIPT></p>";
echo "</td>";

echo "</tr></table><hr />";

echo "<table width=\"100%\" border = 0 align=\"center\" cellpadding=\"10\">";
//
// Affichage des messages
//

echo "<tr><td width = \"40%\" valign=\"top\">";
echo "<span class='grand'>Tous les messages</span><br />";
echo "<span class='small'>Classer par : ";
echo "<a href='index.php?order_by=date_debut'>date début</a> | <a href='index.php?order_by=date_fin'>date fin</a> | <a href='index.php?order_by=id'>date création</a>";
echo "</span><br /><br />";

$appel_messages = mysql_query("SELECT id, texte, date_debut, date_fin, auteur, destinataires FROM messages
WHERE (texte != '') order by ".$order_by." DESC");

$nb_messages = mysql_num_rows($appel_messages);
$ind = 0;
while ($ind < $nb_messages) {
  $content = mysql_result($appel_messages, $ind, 'texte');
  // Mise en forme du texte
  $date_debut1 = mysql_result($appel_messages, $ind, 'date_debut');
  $date_fin1 = mysql_result($appel_messages, $ind, 'date_fin');
  $auteur1 = mysql_result($appel_messages, $ind, 'auteur');
  $destinataires1 = mysql_result($appel_messages, $ind, 'destinataires');
//  $nom_auteur = sql_query1("SELECT nom from utilisateurs where login = '".$auteur1."'");
//  $prenom_auteur = sql_query1("SELECT prenom from utilisateurs where login = '".$auteur1."'");

  $id_message =  mysql_result($appel_messages, $ind, 'id');

//  echo "<b><i>Message de </i></b>: ".$prenom_auteur." ".$nom_auteur.";
   echo "<b><i>Affichage </i></b>: du <b>".strftime("%a %d %b %Y", $date_debut1)."</b> au <b>".strftime("%a %d %b %Y", $date_fin1)."</b>
   <br /><b><i>Destinataire(s) </i></b> : ";
   if (strpos($destinataires1, "p")) echo "professeurs - ";
   if (strpos($destinataires1, "c")) echo "c.p.e. - ";
   if (strpos($destinataires1, "s")) echo "scolarité - ";
   if (strpos($destinataires1, "a")) echo "administrateurs - ";

   echo "<br /><a href='index.php?id_mess=$id_message'>modifier</a>
   - <a href='index.php?id_del=$id_message&action=sup_entry' onclick=\"return confirmlink(this, 'Etes-vous sûr de vouloir supprimer ce message ?', '".$message_suppression."')\">supprimer</a>
   <table border=1 width = '100%' cellpadding='5'><tr><td>".$content."</td></tr></table><br />";
  $ind++;
}
// Fin de la colonne de gauche
echo "</td>";
// Début de la colonne de droite
echo "<td valign=\"top\">";

//
// Affichage du message en modification
//
if (isset($id_mess)) {
    $titre_mess = "Modification d'un message";
    $appel_message = mysql_query("SELECT  id, texte, date_debut, date_fin, auteur, destinataires  FROM messages
    WHERE (id = '".$id_mess."')");
    $contenu = mysql_result($appel_message, 0, 'texte');
    $date_debut = mysql_result($appel_message, 0, 'date_debut');
    $date_fin = mysql_result($appel_message, 0, 'date_fin');
    $destinataires = mysql_result($appel_message, 0, 'destinataires');
    $display_date_debut = strftime("%d", $date_debut)."/".strftime("%m", $date_debut)."/".strftime("%Y", $date_debut);
    $display_date_fin = strftime("%d", $date_fin)."/".strftime("%m", $date_fin)."/".strftime("%Y", $date_fin);
} else {
    $titre_mess = "Nouveau message";
    if (isset($contenu_cor)) $contenu = $contenu_cor ; else $contenu = '';
    if (isset($_POST['display_date_debut']) and isset($_POST['display_date_fin'])) {
        $display_date_debut = $_POST['display_date_debut'];
        $display_date_fin = $_POST['display_date_fin'];
    } else {
        $annee = strftime("%Y");
        $mois = strftime("%m");
        $jour = strftime("%d");
        $display_date_debut = $jour."/".$mois."/".$annee;
        $display_date_fin = $jour."/".$mois."/".$annee;
    }
    if (!isset($destinataires)) $destinataires = '_p';

}
echo "<table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td>";
echo "<form action=\"./index.php\" method=\"post\" style=\"width: 100%;\" name=\"formulaire\">";
if (isset($id_mess)) echo "<input type=\"hidden\" name=\"id_mess\" value=\"$id_mess\" />";
echo "<input type=\"hidden\" name=\"action\" value=\"message\" />";


echo "<table border=\"0\" width = \"100%\" cellspacing=\"1\" cellpadding=\"1\">";

// Titre
echo "<tr><td colspan=\"4\"><span class='grand'>".$titre_mess."</span></td></tr>";
//Enregistrer
echo "<tr><td  colspan=\"4\" align=\"center\"><input type=\"submit\" value=\"Enregistrer\" style=\"font-variant: small-caps;\" name=\"ok\" />";
if (isset($id_mess)) echo "<input type=\"submit\" value=\"Annuler\" style=\"font-variant: small-caps;\" name=\"cancel\" />";

echo "</td></tr>\n";
//Dates
echo "<tr><td colspan=\"4\">";
echo "<i>Le message sera affiché :</i><br />de la date : ";
echo "<input type='text' name = 'display_date_debut' size='8' value = \"".$display_date_debut."\" />";
echo "<a href=\"#\" onClick=\"".$cal1->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>";
echo "&nbsp;à la date : ";
echo "<input type='text' name = 'display_date_fin' size='8' value = \"".$display_date_fin."\" />";
echo "<a href=\"#\" onClick=\"".$cal2->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Calendrier\" /></a>";
echo " (Respectez le format jj/mm/aaaa)</td></tr>";
//Destiantaires
echo "<tr><td  colspan=\"4\"><i>Destinataires du message :</i></td></tr>";
echo "<tr>";
echo "<td><input type=\"checkbox\" name=\"desti_p\" value=\"desti_p\"";
if (strpos($destinataires, "p")) echo "checked";
echo " />Professeurs</td>";

echo "<td><input type=\"checkbox\" name=\"desti_c\" value=\"desti_c\"";
if (strpos($destinataires, "c")) echo "checked";
echo " />C.P.E.</td>";

echo "<td><input type=\"checkbox\" name=\"desti_s\" value=\"desti_s\"";
if (strpos($destinataires, "s")) echo "checked";
echo " />Scolarité</td>";


echo "<td><input type=\"checkbox\" name=\"desti_a\" value=\"desti_a\"";
if (strpos($destinataires, "a")) echo "checked";
echo " />Administrateur</td>";
echo "</tr>";
// Message
echo "<tr><td  colspan=\"4\">";

echo "<i>Mise en forme du message :</i>";

$oFCKeditor = new FCKeditor('message') ;
$oFCKeditor->BasePath = '../fckeditor/' ;  // '/FCKeditor/' is the default value.
$oFCKeditor->Config['DefaultLanguage']      = 'fr' ;
$oFCKeditor->ToolbarSet = 'Basic' ;
$oFCKeditor->Value      = $contenu ;
$oFCKeditor->Create() ;

echo "</td></tr></table>";
echo "</form></td></tr></table>";

// Fin de la colonne de droite

echo "</td></tr></table>";
require("../lib/footer.inc.php");
?>