<?php
/*
 * Last modification  : 23/11/2006
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

require_once("../lib/mincals.inc");
require_once("lib/functions.inc");
// On vérifie si l'accès est restreint ou non
require_once("lib/auth.php");
unset($day);
$day = isset($_POST["day"]) ? $_POST["day"] : (isset($_GET["day"]) ? $_GET["day"] : date("d"));
unset($month);
$month = isset($_POST["month"]) ? $_POST["month"] : (isset($_GET["month"]) ? $_GET["month"] : date("m"));
unset($year);
$year = isset($_POST["year"]) ? $_POST["year"] : (isset($_GET["year"]) ? $_GET["year"] : date("Y"));
$id_classe = isset($_POST["id_classe"]) ? $_POST["id_classe"] : (isset($_GET["id_classe"]) ? $_GET["id_classe"] : -1);
unset($id_groupe);
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);

if (is_numeric($id_groupe)) {
    $current_group = get_group($id_groupe);
} else {
    $current_group = false;
}

// Nom complet de la classe
$appel_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id='$id_classe'");
$classe_nom = @old_mysql_result($appel_classe, 0, "classe");
// Nom complet de la matière

$matiere_nom = $current_group["matiere"]["nom_complet"];
(!isset($_GET['ordre']) or (($_GET['ordre'] != '') and ($_GET['ordre']!= 'DESC')))?$current_ordre='':$current_ordre=$_GET['ordre'];
($current_ordre == '')?$ordre='DESC':$ordre='';
(!isset($_GET['imprime']) or (($_GET['imprime'] != 'y') and ($_GET['imprime']!= 'n')))?$current_imprime='n':$current_imprime=$_GET['imprime'];
if ($current_imprime == 'n') {
  $imprime='y';
  $text_imprime="Version imprimable";
  $largeur = "30%";
} else {
  $imprime='n';
  $text_imprime="Retour";
  $largeur = "5%";
}
//**************** EN-TETE *****************
if ($current_imprime=='n') $titre_page = "Cahier de textes - Vue d'ensemble";
$page_accueil = "index.php?id_classe=-1";
require_once("lib/header_public.inc.php");
//**************** FIN EN-TETE *************
//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    echo("<center><p class='grand'>Le cahier de textes n'est pas accessible pour le moment.</p></center>");
    require ("../lib/footer.inc.php");
    die();
}
if (getSettingValue("cahier_texte_acces_public")!='yes') {
    echo("<center><p class='grand'>Le cahier de textes n'est pas en accès public.</p></center>");
    require ("../lib/footer.inc.php");
    die();
}
echo "<table border='0' width=\"98%\" cellspacing=0 align=\"center\"><tr>";
echo "<td valign='top' width=".$largeur.">";
if ($current_imprime=='n') {
  echo make_classes_select_html('see_all.php', $id_classe, $year, $month, $day);
  if ($id_classe != -1) echo make_matiere_select_html('see_all.php', $id_classe, $id_groupe, $year, $month, $day);
}
echo "</td>";
echo "<td style=\"text-align:center;\">";
echo "<p><span class='grand'>Cahier de textes";
if ($id_classe != -1) echo "<br />$classe_nom";
if ($current_group) echo "- $matiere_nom";
echo "</span>";

  // Test si le cahier de texte est partagé
if ($current_group) {
  echo "<br /><b>(";
  $i=0;
  foreach ($current_group["profs"]["users"] as $prof) {
    if ($i != 0) echo ", ";
    echo mb_substr($prof["prenom"],0,1) . ". " . $prof["nom"];
    $i++;
  }
  echo ")</b>";
}
echo "</p></td>";
echo "</tr></table>";
if ($current_group) {
    if ($current_imprime=='n')
    echo "<a href=see_all.php?id_classe=$id_classe&amp;id_groupe=$id_groupe&amp;ordre=$ordre&amp;imprime=$current_imprime>Trier dans l'ordre inverse</a> - ";
    echo "<a href=see_all.php?id_classe=$id_classe&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$imprime>$text_imprime</a>";
    if ($current_imprime=='n')
    echo " - <a href=index.php?id_classe=$id_classe&amp;id_groupe=$id_groupe>Retour</a> - ";
}
echo "<hr />";
$test_cahier_texte = mysqli_query($GLOBALS["mysqli"], "SELECT contenu  FROM ct_entry WHERE (id_groupe='$id_groupe')");
$nb_test = mysqli_num_rows($test_cahier_texte);
if ($nb_test == 0) {
    echo "<center><h3 class='gepi'>Choisissez une classe et une matière.</h3></center>";
    echo "</body></html>";
    die();
}
// Affichage des informations générales
$appel_info_cahier_texte = mysqli_query($GLOBALS["mysqli"], "SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='')");
$nb_cahier_texte = mysqli_num_rows($appel_info_cahier_texte);
$content = @old_mysql_result($appel_info_cahier_texte, 0, 'contenu');
$id_ct = @old_mysql_result($appel_info_cahier_texte, 0, 'id_ct');
$content .= affiche_docs_joints($id_ct,"c");
if ($content != '') {
    echo "<div  style=\"border-bottom-style: solid; border-width:2px; border-color: ".$couleur_bord_tableau_notice."; \"><b>INFORMATIONS GENERALES</b></div>";
    echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; padding: 2px; margin: 2px;\" width = '100%' cellpadding='5'><tr><td>".$content."</td></tr></table>";
}

echo "<div  style=\"border-bottom-style: solid; border-width:2px; border-color: ".$couleur_bord_tableau_notice."; \"><b>CAHIER DE TEXTES: compte-rendus de séance</b></div><br />";

$current_time = time();
$req_notices =
    "select 'c' type, contenu, date_ct, id_ct
    from ct_entry
    where (contenu != ''
    and id_groupe='".$id_groupe."'
    and date_ct != ''
    and date_ct >= '".getSettingValue("begin_bookings")."'
    and date_ct <= '$current_time'
    and date_ct <= '".getSettingValue("end_bookings")."')
    ORDER BY date_ct ".$current_ordre.", heure_entry ".$current_ordre;
$res_notices = mysqli_query($GLOBALS["mysqli"], $req_notices);
$notice = mysqli_fetch_object($res_notices);

$req_devoirs =
    "select 't' type, contenu, date_ct, id_ct
    from ct_devoirs_entry
    where (contenu != ''
    and id_groupe = '".$id_groupe."'
    and date_ct != ''
    and date_ct >= '".getSettingValue("begin_bookings")."'
    and date_ct <= '".getSettingValue("end_bookings")."'
    ) order by date_ct ".$current_ordre;
$res_devoirs = mysqli_query($GLOBALS["mysqli"], $req_devoirs);
$devoir = mysqli_fetch_object($res_devoirs);

// Boucle d'affichage des notices dans la colonne de gauche
$date_ct_old = -1;
while (true) {
  if ($current_ordre == "DESC") {
    // On met les notices du jour avant les devoirs à rendre aujourd'hui
    if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
        // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
        $not_dev = $notice;
        $notice = mysqli_fetch_object($res_notices);
    } elseif($devoir) {
        // Plus de notices et toujours un devoir, ou devoir plus récent
        $not_dev = $devoir;
        $devoir = mysqli_fetch_object($res_devoirs);
    } else {
        // Plus rien à afficher, on sort de la boucle
        break;
    }
  } else {
    // On met les notices du jour avant les devoirs à rendre aujourd'hui
    if ($notice && (!$devoir || $notice->date_ct <= $devoir->date_ct)) {
        // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
        $not_dev = $notice;
        $notice = mysqli_fetch_object($res_notices);
    } elseif($devoir) {
        // Plus de notices et toujours un devoir, ou devoir plus récent
        $not_dev = $devoir;
        $devoir = mysqli_fetch_object($res_devoirs);
    } else {
        // Plus rien à afficher, on sort de la boucle
        break;
    }
  }
    $content = &$not_dev->contenu;
    $content .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);

    if ($not_dev->type == "t") {
        echo("<strong>A faire pour le : </strong>\n");
    }
    echo("<b>" . strftime("%a %d %b %y", $not_dev->date_ct) . "</b>\n");
    // Numérotation des notices si plusieurs notice sur la même journée
    if ($not_dev->type == "c") {
      if ($date_ct_old == $not_dev->date_ct) {
        $num_notice++;
        echo " <b><i>(notice N° ".$num_notice.")</i></b>";
      } else {
        // on afffiche "(notice N° 1)" uniquement s'il y a plusieurs notices dans la même journée
        $nb_notices = sql_query1("SELECT count(id_ct) FROM ct_entry WHERE (id_groupe='" . $current_group["id"] ."' and date_ct='".$not_dev->date_ct."')");
        if ($nb_notices > 1)
            echo " <b><i>(notice N° 1)</i></b>";
        // On réinitialise le compteur
        $num_notice = 1;
      }
    }
    echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices[$not_dev->type]."\">\n<tr>\n<td>\n$content</td>\n</tr>\n</table>\n<br/>\n");
    if ($not_dev->type == "c") $date_ct_old = $not_dev->date_ct;
}

//if ($current_imprime=='n') echo "</td></tr></table>";
//echo "</td></tr></table>";
require("../lib/footer.inc.php");
?>