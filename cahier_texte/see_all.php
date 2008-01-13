<?php
/*
 * @version: $Id$
 *
 * Copyright 2001, 2007 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Gabriel Fischer
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
require_once("../lib/transform_functions.php");

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

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("Le module n'est pas activé.");
}

//include "../lib/mincals.inc";


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

unset($selected_eleve);
$login_eleve = isset($_POST["login_eleve"]) ? $_POST["login_eleve"] :(isset($_GET["login_eleve"]) ? $_GET["login_eleve"] :false);
if ($login_eleve) {
	$selected_eleve = mysql_fetch_object(mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e WHERE login = '" . $login_eleve . "'"));
} else {
	$selected_eleve = false;
}

if ($_SESSION['statut'] == 'eleve') {
	$selected_eleve = mysql_fetch_object(mysql_query("SELECT e.login, e.nom, e.prenom FROM eleves e WHERE login = '".$_SESSION['login'] . "'"));
} elseif ($_SESSION['statut'] == "responsable") {
	$get_eleves = mysql_query("SELECT e.login, e.nom, e.prenom " .
			"FROM eleves e, resp_pers r, responsables2 re " .
			"WHERE (" .
			"e.ele_id = re.ele_id AND " .
			"re.pers_id = r.pers_id AND " .
			"r.login = '".$_SESSION['login']."')");

	if (mysql_num_rows($get_eleves) == 1) {
			// Un seul élève associé : on initialise tout de suite la variable $selected_eleve
			// Cela signifie entre autre que l'on ne prend pas en compte $login_eleve, fermant ainsi une
			// potentielle faille de sécurité.
		$selected_eleve = mysql_fetch_object($get_eleves);
	} elseif (mysql_num_rows($get_eleves) == 0) {
		$selected_eleve = false;
	} elseif (mysql_num_rows($get_eleves) > 1 and $selected_eleve) {
		// Si on est là, c'est que la variable $login_eleve a été utilisée pour
		// générer $selected_eleve
		// On va vérifier que l'élève ainsi sélectionné fait bien partie des élèves
		// associés à l'utilisateur au statut 'responsable'
		$ok = false;
		while($test = mysql_fetch_object($get_eleves)) {
			if ($test->login == $selected_eleve->login) $ok = true;
		}
		if (!$ok) $selected_eleve = false;
	}
}
$selected_eleve_login = $selected_eleve ? $selected_eleve->login : "";

// Nom complet de la classe
$appel_classe = mysql_query("SELECT classe FROM classes WHERE id='$id_classe'");
$classe_nom = @mysql_result($appel_classe, 0, "classe");
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
require_once("../lib/header.inc");
//**************** FIN EN-TETE *************
//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
    die("<center><p class='grand'>Le cahier de textes n'est pas accessible pour le moment.</p></center>");
}
echo "<table border='0' width=\"98%\" cellspacing=0 align=\"center\"><tr>";
echo "<td valign='top' width=".$largeur.">";
if ($current_imprime=='n') {
	if ($_SESSION['statut'] == 'responsable') {
		echo make_eleve_select_html('see_all.php', $_SESSION['login'], $selected_eleve, $year, $month, $day);
	}
	if ($selected_eleve_login != "") echo make_matiere_select_html('see_all.php', $selected_eleve_login, $id_groupe, $year, $month, $day);

	if ($_SESSION['statut'] != "responsable" and $_SESSION['statut'] != "eleve") {
  		echo make_classes_select_html('see_all.php', $id_classe, $year, $month, $day);
  		if ($id_classe != -1) echo make_matiere_select_html('see_all.php', $id_classe, $id_groupe, $year, $month, $day);
	}
}
echo "</td>";
echo "<td style=\"text-align:center;\">";
echo "<p><span class='grand'>Cahier de textes";
if ($current_group) {
	echo " - $matiere_nom";
	echo " - classe de ".$current_group['classlist_string'];
}
if ($id_classe != -1) {
	echo "<br />$classe_nom";
}
echo "</span>";

  // Test si le cahier de texte est partagé
if ($current_group) {
  echo "<br /><b>(";
  $i=0;
  foreach ($current_group["profs"]["users"] as $prof) {
    if ($i != 0) echo ", ";
    echo substr($prof["prenom"],0,1) . ". " . $prof["nom"];
    $i++;
  }
  echo ")</b>";
}
echo "</p></td>";
echo "</tr></table>";
if ($current_group) {
    if ($current_imprime=='n') {
    if ($_SESSION["statut"] == "professeur" OR $_SESSION["statut"] == "scolarite" OR $_SESSION["statut"] == "cpe") {
    	echo "<a href='see_all.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> - ";
    	if ($_SESSION["statut"] == "professeur") {
    		echo "<a href='./index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour vers mes cahiers de textes</a> - ";
    	}

    } else {
    echo "<a href='consultation.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> - ";
    }
    if ($current_imprime=='n')
    echo "<a href=see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$ordre&amp;imprime=$current_imprime>Trier dans l'ordre inverse</a> - ";
    echo "<a href=see_all.php?id_classe=$id_classe&amp;login_eleve=$selected_eleve_login&amp;id_groupe=$id_groupe&amp;ordre=$current_ordre&amp;imprime=$imprime>$text_imprime</a>";
}
}
echo "<hr />";
$test_cahier_texte = mysql_query("SELECT contenu  FROM ct_entry WHERE (id_groupe='$id_groupe')");
$nb_test = mysql_num_rows($test_cahier_texte);
if ($nb_test == 0) {
	echo "<center>";
	if ($_SESSION['statut'] == "responsable") {
		echo "<h3 class='gepi'>Choisissez un élève et une matière.</h3>\n";
	} elseif ($_SESSION['statut'] == "eleve") {
		echo "<h3 class='gepi'>Choisissez une matière</h3>\n";
	} else {
		echo "<h3 class='gepi'>Choisissez une classe et une matière.</h3>\n";
	}
	echo "</center>";
	require("../lib/footer.inc.php");
	die();
}
// Affichage des informations générales
$appel_info_cahier_texte = mysql_query("SELECT contenu, id_ct  FROM ct_entry WHERE (id_groupe='$id_groupe' and date_ct='')");
$nb_cahier_texte = mysql_num_rows($appel_info_cahier_texte);
$content = @mysql_result($appel_info_cahier_texte, 0, 'contenu');
$id_ct = @mysql_result($appel_info_cahier_texte, 0, 'id_ct');
include "../lib/transform.php";
$html .= affiche_docs_joints($id_ct,"c");
if ($html != '') {
    echo "<div  style=\"border-bottom-style: solid; border-width:2px; border-color: ".$couleur_bord_tableau_notice."; \"><b>INFORMATIONS GENERALES</b></div>";
    echo "<table style=\"border-style:solid; border-width:0px; border-color: ".$couleur_bord_tableau_notice."; padding: 2px; margin: 2px;\" width = '100%' cellpadding='5'><tr><td>".$html."</td></tr></table>";
}

echo "<div  style=\"border-bottom-style: solid; border-width:2px; border-color: ".$couleur_bord_tableau_notice."; \"><b>CAHIER DE TEXTES: compte-rendus de séance</b></div><br />";

$req_notices =
    "select 'c' type, contenu, date_ct, id_ct
    from ct_entry
    where (contenu != ''
    and id_groupe='".$id_groupe."'
    and date_ct != ''
    and date_ct >= '".getSettingValue("begin_bookings")."'
    and date_ct <= '".getSettingValue("end_bookings")."')
    ORDER BY date_ct ".$current_ordre.", heure_entry ".$current_ordre;
$res_notices = mysql_query($req_notices);
$notice = mysql_fetch_object($res_notices);

$req_devoirs =
    "select 't' type, contenu, date_ct, id_ct
    from ct_devoirs_entry
    where (contenu != ''
    and id_groupe = '".$id_groupe."'
    and date_ct != ''
    and date_ct >= '".getSettingValue("begin_bookings")."'
    and date_ct <= '".getSettingValue("end_bookings")."'
    ) order by date_ct ".$current_ordre;
$res_devoirs = mysql_query($req_devoirs);
$devoir = mysql_fetch_object($res_devoirs);

// Boucle d'affichage des notices dans la colonne de gauche
$date_ct_old = -1;
while (true) {
  if ($current_ordre == "DESC") {
    // On met les notices du jour avant les devoirs à rendre aujourd'hui
    if ($notice && (!$devoir || $notice->date_ct >= $devoir->date_ct)) {
        // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
        $not_dev = $notice;
        $notice = mysql_fetch_object($res_notices);
    } elseif($devoir) {
        // Plus de notices et toujours un devoir, ou devoir plus récent
        $not_dev = $devoir;
        $devoir = mysql_fetch_object($res_devoirs);
    } else {
        // Plus rien à afficher, on sort de la boucle
        break;
    }
  } else {
    // On met les notices du jour avant les devoirs à rendre aujourd'hui
    if ($notice && (!$devoir || $notice->date_ct <= $devoir->date_ct)) {
        // Il y a encore une notice et elle est plus récente que le prochain devoir, où il n'y a plus de devoirs
        $not_dev = $notice;
        $notice = mysql_fetch_object($res_notices);
    } elseif($devoir) {
        // Plus de notices et toujours un devoir, ou devoir plus récent
        $not_dev = $devoir;
        $devoir = mysql_fetch_object($res_devoirs);
    } else {
        // Plus rien à afficher, on sort de la boucle
        break;
    }
  }
    // Passage en HTML
    $content = &$not_dev->contenu;
    include ("../lib/transform.php");
    $html .= affiche_docs_joints($not_dev->id_ct,$not_dev->type);

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
    echo("<table style=\"border-style:solid; border-width:1px; border-color: ".$couleur_bord_tableau_notice.";\" width=\"100%\" cellpadding=\"1\" bgcolor=\"".$color_fond_notices[$not_dev->type]."\">\n<tr>\n<td>\n$html</td>\n</tr>\n</table>\n<br/>\n");
    if ($not_dev->type == "c") $date_ct_old = $not_dev->date_ct;
}

//if ($current_imprime=='n') echo "</td></tr></table>";
//echo "</td></tr></table>";
require("../lib/footer.inc.php");
?>