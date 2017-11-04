<?php
/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// filtage des items à afficher
if (!isset($_SESSION['items_a_afficher'])) {$_SESSION['items_a_afficher'] = "ouverts";}
if (isset($_POST['post_items_a_afficher'])) {
	$_SESSION['items_a_afficher']=$_POST['items_a_afficher'];
}

if (!isset($_SESSION['order_by'])) {$_SESSION['order_by'] = "date ASC";}
$_SESSION['order_by'] = isset($_POST['order_by']) ? $_POST['order_by'] : (isset($_GET['order_by']) ? $_GET['order_by'] : $_SESSION['order_by']);
$order_by = $_SESSION['order_by'];
$id_inter = isset($_POST['id_inter']) ? $_POST['id_inter'] : (isset($_GET['id_inter']) ? $_GET['id_inter'] : NULL);


// Suppression d'un item
if (isset($_GET['action']) and ($_GET['action'] == "supprimer")) {
    $del = mysqli_query($GLOBALS["mysqli"], "delete from inscription_j_login_items where id='".$_GET['id_inter']."'");
    $del = mysqli_query($GLOBALS["mysqli"], "delete from inscription_items where id='".$_GET['id_inter']."'");
    $msg = "Les modifications ont été enregistrées.";
}

// Enregistrements

if (isset($_POST['activer']) && !saveSetting("active_inscription_utilisateurs", $_POST['activer']))
		$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";

if (isset($_POST['is_posted_notes'])) {
  check_token();

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

	if((!preg_match("#[0-9]{2}/[0-9]{2}/[0-9]{4}#", $_POST['date']))&&(!preg_match("#[0-9]{4}/[0-9]{2}/[0-9]{2}#", $_POST['date']))) {
		$msg="La date saisie (<em>".$_POST['date']."</em>) est invalide (<em>pas au format attendu.</em>)<br />";
	}
	else {

		if(preg_match("#[0-9]{4}/[0-9]{2}/[0-9]{2}#", $_POST['date'])) {
			$date_choisie=$_POST['date'];
		}
		else {
			$tmp_tab=explode("/", $_POST['date']);
			$date_choisie=$tmp_tab[2]."/".$tmp_tab[1]."/".$tmp_tab[0];
		}

		$msg = "";
		if ($_POST['is_posted'] == "ajout") {
  $req = mysqli_query($GLOBALS["mysqli"], "insert into inscription_items set
  date='".$date_choisie."',
  heure='".$_POST['heure']."',
  description='".$_POST['description']."'
  ");
		} else {
  $req = mysqli_query($GLOBALS["mysqli"], "update inscription_items set
  date='".$date_choisie."',
  heure='".$_POST['heure']."',
  description='".$_POST['description']."'
  where id = '".$_POST['id_inter']."'
  ");
		}
		$msg .= "Les modifications ont été enregistrées.";
	}
}

$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM inscription_items ORDER BY $order_by");
$nombre_lignes = mysqli_num_rows($call_data);

if (!loadSettings()) {
    die("Erreur chargement settings");
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

//**************** EN-TETE *****************
$titre_page = "Configuration du module Inscription";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>
<script src="../ckeditor_4/ckeditor.js"></script>
<?php

// Ajout d'un item
if (isset($_GET['action']) and ($_GET['action'] == "ajout")) {
    echo "<p class=bold><a href=\"./inscription_config.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href=\"./inscription_config.php?action=ajout\">Ajouter un item</a> | <a href=\"javascript:centrerpopup('help.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">Aide</a></p>\n";

    echo "<form name=\"formulaire2\" method=\"post\" action=\"inscription_config.php\">";

	echo add_token_field();

    if (isset($id_inter)) {
        $req = mysqli_query($GLOBALS["mysqli"], "select * from inscription_items where id='".$id_inter."'");
        $date = htmlspecialchars(@old_mysql_result($req, 0, "date"));
        $heure = htmlspecialchars(@old_mysql_result($req, 0, "heure"));
        $description = htmlspecialchars(@old_mysql_result($req, 0, "description"));
        echo "<input type=\"hidden\" name=\"is_posted\" value=\"modif\" />\n";
        echo "<input type=\"hidden\" name=\"id_inter\" value=\"".$id_inter."\" />\n";
    } else {
        $date = "";
        $heure = "";
        $description = "";
        echo "<input type=\"hidden\" name=\"is_posted\" value=\"ajout\" />\n";
    }
    echo "<H2>Ajout d'un item</H2>\n";
    echo "<p>un item correspond à une entité (<em>stage, intervention dans les établissements, réunion,...</em>) à laquelle les utilisateurs peuvent s'inscrire.</p>\n";
    echo "<table cellpadding=\"6\">\n";
    echo "<tr><td>Date<br />(<em>au format AAAA/MM/JJ ou JJ/MM/AAAA</em>) : </td><td style='vertical-align:bottom;'><input type=\"text\" name=\"date\" id=\"date_item\" value=\"$date\" size=\"20\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" title=\"Vous pouvez modifier la date à l'aide des flèches Up et Down du pavé de direction.\" />";
    echo img_calendrier_js("date_item", "img_bouton_date_item");
    echo "</td></tr>\n";
    echo "<tr><td>Heure : </td><td><input type=\"text\" name=\"heure\" value=\"$heure\" size=\"20\" /></td></tr>\n";
    echo "<tr><td>Description (<em>lieu, ...</em>) : </td><td><input type=\"text\" name=\"description\" value=\"$description\" size=\"50\" /></td></tr>\n";

    echo "</table>\n";
    echo "<input type=\"submit\" name=\"Enregistrer\" value=\"Envoyer\" />\n";
    echo "</form>\n";
    require("../lib/footer.inc.php");
    die();
}

echo "<p class=bold><a href=\"../accueil.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href=\"./inscription_config.php?action=ajout\">Ajouter un item</a> | <a href=\"javascript:centrerpopup('help.php',800,500,'scrollbars=yes,statusbar=no,resizable=yes')\">Aide</a></p>\n";
echo "<form name=\"formulaire2\" method=\"post\"  action=\"inscription_config.php\">\n";
echo add_token_field();
echo "<H2>Activation  / Désactivation</H2>\n";
$active_prof = getSettingValue("active_inscription_utilisateurs");
if ($active_prof == "y") {
  echo "Actuellement, la page autorisant les inscriptions est activée : les utilisateurs (<em>professeurs, cpe, scolarité</em>) peuvent donc s'inscrire.";
} else {
  echo "Actuellement, la page autorisant les inscriptions n'est pas activée : les utilisateurs (<em>professeurs, cpe, scolarité</em>) ne peuvent pas s'inscrire.";
}
?>
<br /> <label for='activer_y' style='cursor: pointer;'><input type="radio" name="activer" id="activer_y" value="y" <?php if (getSettingValue("active_inscription_utilisateurs")=='y') echo " checked"; ?> />
&nbsp;Activer l'acc&egrave;s aux inscriptions</label><br />
<label for='activer_n' style='cursor: pointer;'><input type="radio" name="activer" id="activer_n" value="n" <?php if (getSettingValue("active_inscription_utilisateurs")=='n') echo " checked"; ?> />
&nbsp;Désactiver l'accès aux inscriptions</label>
<br />
<center><input type="submit" name="ok" value="Enregistrer" /></center>
</form>



<?php
echo "<hr />";
echo "<a name='liste'></a>";
echo "<H2>Liste des items</H2>\n";
if ($nombre_lignes != 0) {
  echo "<p>Chaque item ci-dessous correspond à une entité (<em>stage, intervention dans les établissements, réunion,...</em>) à laquelle les utilisateurs peuvent s'inscrire.</p>\n";
?>

<br />
<form id="form_items_a_afficher" method="post" action="inscription_config.php#liste">
<p align="center">
Afficher les items :  
<input type="radio" name="items_a_afficher" id='items_a_afficher' value='ouverts' <?php if ($_SESSION['items_a_afficher']=="ouverts") echo "checked "; ?>onchange="document.getElementById('form_items_a_afficher').submit();" /><label style='cursor: pointer;'> ouverts</label> | 
<input type="radio" name="items_a_afficher" id='items_a_afficher' value='tous' <?php if ($_SESSION['items_a_afficher']=="tous") echo "checked "; ?>onchange="document.getElementById('form_items_a_afficher').submit();" /><label style='cursor: pointer;'> tous</label> | 
<input type="radio" name="items_a_afficher" id='items_a_afficher' value='clos' <?php if ($_SESSION['items_a_afficher']=="clos") echo "checked "; ?>onchange="document.getElementById('form_items_a_afficher').submit();" /><label style='cursor: pointer;'> clos</label>
<input type="hidden" name="post_items_a_afficher" />
</p><br />
</form>

<?php

  echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"5\">\n";
  echo "<tr>\n";
  echo "<td><p class='bold'>N°<a href='inscription_config.php?order_by=id%20ASC#liste'><img src=\"../images/sort_up.gif\" style=\"vertical-align:middle\"></a><a href='inscription_config.php?order_by=id%20DESC#liste'><img src=\"../images/sort_dn.gif\" style=\"vertical-align:middle\"></a></p></td>\n";
  echo "<td><p class='bold'>Date<a href='inscription_config.php?order_by=date%20ASC#liste'><img src=\"../images/sort_up.gif\" style=\"vertical-align:middle\"></a><a href='inscription_config.php?order_by=date%20DESC#liste'><img src=\"../images/sort_dn.gif\" style=\"vertical-align:middle\"></a></p></td>\n";
  echo "<td><p class='bold'>Heure</p></td>\n";
  echo "<td><p class='bold'>Intitulé<a href='inscription_config.php?order_by=description%20ASC#liste'><img src=\"../images/sort_up.gif\" style=\"vertical-align:middle\"></a><a href='inscription_config.php?order_by=description%20DESC#liste'><img src=\"../images/sort_dn.gif\" style=\"vertical-align:middle\"></a></p></td>\n";
  echo "<td><p class='bold'>Personnes actuellement inscrites</p></td>\n";
  echo "<td><p class='bold'>-</p></td>\n";
  echo "</tr>\n";
  $_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];
  $i = 0;
  $aujourdhui=date('Y/m/d');
  while ($i < $nombre_lignes){
    $id = old_mysql_result($call_data, $i, "id");
    $date = old_mysql_result($call_data, $i, "date");
	if (($_SESSION['items_a_afficher']=="tous") || ($_SESSION['items_a_afficher']=="ouverts" && $date>$aujourdhui) || ($_SESSION['items_a_afficher']=="clos" && $date<=$aujourdhui)) {
		$heure = old_mysql_result($call_data, $i, "heure");
		$description = old_mysql_result($call_data, $i, "description");

		$day = mb_substr($date, 8, 2);
		$month = mb_substr($date, 5, 2);
		$year = mb_substr($date, 0, 4);
		
		$f_date = french_strftime("%A %d %B %Y", mktime(0,0,0,$month,$day,$year));


		$inscrit = sql_query1("select id from inscription_j_login_items
		where login='".$_SESSION['login']."' and id='".$id."' ");

		$inscrits = mysqli_query($GLOBALS["mysqli"], "select login from inscription_j_login_items
		where id='".$id."' ");
		$nb_inscrits = mysqli_num_rows($inscrits);
		if ($nb_inscrits == 0) $noms_inscrits = "<center>-</center>"; else $noms_inscrits = "";
		$k = 0;
		while ($k < $nb_inscrits) {
			$login_inscrit = old_mysql_result($inscrits, $k, "login");
			$nom_inscrit = sql_query1("select nom from utilisateurs where login='".$login_inscrit."'");
			if ($nom_inscrit == -1) $nom_inscrit = "<font color='red'>(Nom absent => login : ".$login_inscrit.")</font>";
			$prenom_inscrit = sql_query1("select prenom from utilisateurs where login='".$login_inscrit."'");
			if ($prenom_inscrit == -1) $prenom_inscrit = "";
			$noms_inscrits .=$prenom_inscrit." ".$nom_inscrit."<br />";
			$k++;
		}
		echo "<tr>\n";
		echo "<td>$id <br />";
		if ($date>$aujourdhui || $_SESSION['statut']=="administrateur" ) echo "<a href=\"./inscription_config.php?id_inter=$id&amp;action=ajout\" >Modifier</a>";
		echo "</td>\n";
		echo "<td>$f_date</td>\n";
		echo "<td>$heure</td>\n";
		echo "<td>$description</td>\n";
		echo "<td>".$noms_inscrits."</td>\n";
		echo "<td style=\"vertical-align:middle; text-align: center;\">"; 
		if ($date>$aujourdhui || $_SESSION['statut']=="administrateur" ) echo "<a href=\"./inscription_config.php?id_inter=$id&amp;action=supprimer\" onclick=\"javascript:return confirm('Êtes-vous sûr de vouloir supprimer cet item ?')\">Supprimer</a>";
		echo "</td>\n";
		echo "</tr>\n";
	}
    $i++;
  }
  echo "</table>\n";
} else {
  echo "<p>Actuellement aucun item n'est présent dans la base.";
}


echo "<hr />";
$contenu = getSettingValue("mod_inscription_explication");

echo "<form name=\"formulaire3\" method=\"post\"  action=\"inscription_config.php\">\n";
echo add_token_field();
echo "<H2>Titre du module</H2>\n";
echo "<input type=\"text\" name=\"mod_inscription_titre\" size=\"40\" value=\"".getSettingValue("mod_inscription_titre")."\" />\n";
echo "<H2>Texte explicatif</H2>\n";
echo "<p>Le texte ci-dessous sera visible par les personnes accédant au module d'inscription/désincription.</p>\n";
echo "<input type=\"hidden\" name=\"is_posted_notes\" value=\"yes\" />\n";
    // lancement de CKeditor

?>

<textarea name="no_anti_inject_notes" id ="no_anti_inject_notes" style="border: 1px solid gray; width: 600px; height: 250px;"><?php echo $contenu; ?></textarea>
<script type='text/javascript'>
// Configuration via JavaScript
CKEDITOR.replace('no_anti_inject_notes',{
    customConfig: '../lib/ckeditor_gepi_config_mini.js'
});
</script>

<?php
//echo "<div id=\"fixe\">\n";
echo "<center>";
echo "<input type=\"submit\" name=\"ok\" value=\"Enregistrer\" />";
echo "</center>\n";
//echo "</div>\n";
echo "</form>\n";
require("../lib/footer.inc.php");?>
