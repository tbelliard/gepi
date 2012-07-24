<?php
/*
 *
 * Copyright 2009-2011 Josselin Jacquard, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

header('Content-Type: text/html; charset=utf-8');

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) OR isset($_POST['traite_anti_inject'])) {$traite_anti_inject = "yes";}
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	//header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	//header("Location: ../logout.php?auto=1");
	die();
}

// INSERT INTO droits SET id='/cahier_texte_2/ajax_liste_notices_privees.php',administrateur='F',professeur='V',cpe='F',scolarite='F',eleve='F',responsable='F',secours='F',autre='F',description='Cahiers de textes : Liste des notices privées',statut='';

//echo "plop<br />";

if (!checkAccess()) {
	//header("Location: ../logout.php?auto=1");
	die();
}

//echo "plip<br />";

//On vérifie si le module est activé
if (getSettingValue("active_cahiers_texte")!='y') {
	die("Le module n'est pas activé.");
}

$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Récupération des variables
$id_groupe=isset($_POST["id_groupe"]) ? $_POST["id_groupe"] : (isset($_GET["id_groupe"]) ? $_GET["id_groupe"] : NULL);
$today=isset($_POST["today"]) ? $_POST["today"] : (isset($_GET["today"]) ? $_GET["today"] : NULL);

//debug_var();

// Contrôler que le prof est associé à cette classe?

$id_groupe=preg_replace("/[^0-9]/","",$id_groupe);
if($today!="all") {
	$today=preg_replace("/[^0-9]/","",$today);
}

if($id_groupe=="") {
	echo "<p style='color:red;'>L'identifiant de groupe est incorrect.</p>";
}
elseif($today=="") {
	echo "<p style='color:red;'>Le format de la date (<i>timestamp</i>) est incorrect.</p>";
}
else {
	require("cdt_lib.php");

	echo "<div style='float:right; width: 10em; text-align: right;'>\n";
	if($today!='all') {
		// Voir toutes les notices privées du groupe
		echo " <a href=\"javascript:
						getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$id_groupe."&today=all');
						\">Toutes les NP</a>\n";
	}
	else {
		echo " <a href=\"javascript:
						getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe=".$id_groupe."&today='+getCalendarUnixDate());
						\">NP du jour</a>\n";
	}
	echo "</div>\n";

	$groups=get_groups_for_prof($_SESSION['login']);
	if(count($groups)==1) {
		$current_group=$groups[0];
		echo "<p class='bold'>".$current_group['name']." (<em>".$current_group['description']."</em>) en ".$current_group['classlist_string']."</p>\n";
	}
	else {
		echo "<form enctype=\"multipart/form-data\" name=\"form_choix_jour_np\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
		//echo "<select name='id_groupe' onchange=\"document.forms['form_choix_jour_np'].submit()\">\n";
		//echo "<select name='id_groupe' id='id_groupe' onchange=\"id_groupe=(\$A($('id_groupe').options).find(function(option) { return option.selected; }).value);";
		//echo "<select name='id_groupe' id='id_groupe' onchange=\"id_groupe=document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value;";
		//echo "<select name='id_groupe' id='id_groupe' onchange=\"id_groupe=document.getElementById('id_groupe').selectedIndex;";
		//echo "alert(id_groupe);";
		//echo "<select name='id_groupe' id='id_groupe' onchange=\"alert(document.getElementById('id_groupe').selectedIndex);";
		//echo "<select name='id_groupe' id='id_groupe' onchange=\"alert(document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value);";
		//echo "getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe='+id_groupe+'&today=$today',{ onComplete:function(transport) {initWysiwyg();}});";

		echo "<select name='id_groupe' id='id_groupe' onchange=\"";
		//echo "getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe='+document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value+'&today=$today',{ onComplete:function(transport) {initWysiwyg();}});";
		echo "getWinListeNoticesPrivees().setAjaxContent('./ajax_liste_notices_privees.php?id_groupe='+document.getElementById('id_groupe').options[document.getElementById('id_groupe').selectedIndex].value+'&today=$today');";

		echo "\">\n";
		foreach($groups as $current_group) {
			$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$current_group['id']."' AND domaine='cahier_texte' AND visible='n';";
			$test_grp_visib=mysql_query($sql);
			if(mysql_num_rows($test_grp_visib)==0) {
				echo "<option value='".$current_group['id']."'";
				if($current_group['id']==$id_groupe) {echo " selected='true'";}
				echo ">".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</option>\n";
			}
		}
		echo "</select>\n";
		echo "<br />\n";

	echo "<input type='hidden' name='today' value='$today' />\n";
		echo "</form>\n";
	}

	if($today=="all") {
		echo affiche_toutes_notices_privees_groupe($id_groupe);
	}
	else {
		echo affiche_notice_privee_groupe_jour($id_groupe, strftime("%d/%m/%Y", $today));
	}
}

?>
