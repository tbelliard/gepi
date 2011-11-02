<?php
/*
 *
 *$Id: index.php 5946 2010-11-22 16:02:12Z crob $
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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
$accessibilite="y";
$titre_page = "Gestion du module absence";
$niveau_arbo = 2;
$gepiPathJava="./../..";
$post_reussi=FALSE;
$msg = '';

// $niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");
//mes fonctions
include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

if (isset($_POST['is_posted']) and ($msg=='')) {
	check_token();
	// $msg = '';
	if (isset($_POST['activer'])) {
	// on n'enregistre pas la désactivation du module si mod_abs2 est actif
	if (!((getSettingValue("active_module_absence")!='y')&& $_POST['activer']=="n")) {
		if (!saveSetting("active_module_absence", $_POST['activer'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
		}
	}
	}
	if (isset($_POST['activer_prof'])) {
		if (!saveSetting("active_module_absence_professeur", $_POST['activer_prof'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
		}
	}
	if (isset($_POST['activer_resp'])) {
		if (!saveSetting("active_absences_parents", $_POST['activer_resp'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
		}
	}
	
	if (isset($_POST['classement'])) {
		if (!saveSetting("absence_classement_top", $_POST['classement'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre de classementdes absences (TOP 10) !";
		}
	}
	
	if (isset($_POST['is_posted']) and ($msg=='')) {
	$msg = "Les modifications ont été enregistrées !";
		$post_reussi=TRUE;
	}
}

// A propos du TOP 10 : récupération du setting pour le select en bas de page
$selected10 = $selected20 = $selected30 = $selected40 = $selected50 = NULL;

if (getSettingValue("absence_classement_top") == '10'){
  $selected10 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '20') {
  $selected20 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '30') {
  $selected30 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '40') {
  $selected40 = ' selected="selected"';
}elseif (getSettingValue("absence_classement_top") == '50') {
  $selected50 = ' selected="selected"';
}

// header
// $titre_page = "Gestion du module absence";
//require_once("../../lib/header.inc");


// ====== Inclusion des balises head et du bandeau =====
include_once("../../lib/header_template.inc");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion Absences"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

$lien_sup=array();
$a=0;
$req_setting = mysql_fetch_array(mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_admin'")) OR DIE ('Erreur requête req_setting () : '.mysql_error());
$req_setting2 = mysql_fetch_array(mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_tous'")) OR DIE ('Erreur requête req_setting2 () : '.mysql_error());
if ($req_setting["value"] == 'y' OR $req_setting2["value"] == 'y') {
 // On initialise le $_SESSION["retour"] pour pouvoir revenir proprement
  $_SESSION["retour"] = "../mod_absences/admin/index";
  $lien_sup[$a]['adresse'] = "../../edt_organisation/edt_calendrier.php";
  $lien_sup[$a]['texte'] = "Définir périodes de vacances et jours fériés";
  $a++;
} else {
  $lien_sup[$a]['adresse'] = "admin_config_calendrier.php?action=visualiser";
  $lien_sup[$a]['texte'] = "Définir périodes de vacances et jours fériés";
}


/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../../templates/'.$_SESSION['rep_gabarits'].'/mod_absences/admin/index_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);



/*

echo "<p class=bold><a href=\"../../accueil_modules.php\"><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo "</p>";
?>
<h2>Gestion des absences par les CPE</h2>
<p style="font-style: italic;">La désactivation du module de la gestion des absences n'entraîne aucune
suppression des données. Lorsque le module est désactivé, les CPE n'ont pas accès au module.</p>

<form action="index.php" name="form1" method="post">
<p>
	<input type="radio" id="activerY" name="activer" value="y"
	<?php if (getSettingValue("active_module_absence")=='y') echo ' checked="checked"'; ?> />
	<label for="activerY">&nbsp;Activer le module de la gestion des absences</label>
</p>
<p>
	<input type="radio" id="activerN" name="activer" value="n"
	<?php if (getSettingValue("active_module_absence")=='n') echo ' checked="checked"'; ?> />
	<label for="activerN">&nbsp;Désactiver le module de la gestion des absences</label>
	<input type="hidden" name="is_posted" value="1" />
</p>

<h2>Saisie des absences par les professeurs</h2>
<p style="font-style: italic;">La désactivation du module de la gestion des absences n'entraîne aucune suppression des données saisies par les professeurs. Lorsque le module est désactivé, les professeurs n'ont pas accès au module.
Normalement, ce module ne devrait être activé que si le module ci-dessus est lui-même activé.</p>
<p>
	<input type="radio" id="activerProfY" name="activer_prof" value="y"
	<?php if (getSettingValue("active_module_absence_professeur")=='y') echo " checked='checked'"; ?> />
	<label for="activerProfY">&nbsp;Activer le module de la saisie des absences par les professeurs</label>
	<a href="./interface_abs.php">&nbsp;Param&eacute;trer l'interface des professeurs</a>
</p>
<p>
	<input type="radio" id="activerProfN" name="activer_prof" value="n"
	<?php if (getSettingValue("active_module_absence_professeur")=='n') echo " checked='checked'"; ?> />
	<label for="activerProfN">&nbsp;Désactiver le module de la saisie des absences par les professeurs</label>
	<input type="hidden" name="is_posted" value="1" />
</p>

<h2>G&eacute;rer l'acc&egrave;s des responsables d'&eacute;l&egrave;ves</h2>
<p style="font-style: italic">Vous pouvez permettre aux responsables d'acc&eacute;der aux donn&eacute;es brutes
entr&eacute;es dans Gepi par le biais du module absences.</p>
<p>
	<input type="radio" id="activerRespOk" name="activer_resp" value="y"
	<?php if (getSettingValue("active_absences_parents") == 'y') echo ' checked="checked"'; ?> />
	<label for="activerRespOk">Permettre l'acc&egrave;s aux responsables</label>
</p>
<p>
	<input type="radio" id="activerRespKo" name="activer_resp" value="n"
	<?php if (getSettingValue("active_absences_parents") == 'n') echo ' checked="checked"'; ?> />
	<label for="activerRespKo">Ne pas permettre cet acc&egrave;s</label>
</p>

<h2>Param&eacute;trer le classement des absences (par d&eacute;faut TOP 10)</h2>
<p>
  <label for="idClass">Nombre de lignes pour le classement</label>
  <select id="idCLass" name="classement">
    <option value="10"<?php echo $selected10; ?>>10</option>
    <option value="20"<?php echo $selected20; ?>>20</option>
    <option value="30"<?php echo $selected30; ?>>30</option>
    <option value="40"<?php echo $selected40; ?>>40</option>
    <option value="50"<?php echo $selected50; ?>>50</option>
  </select>
</p>

<div class="centre"><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></div>
</form>

<h2>Configuration avancée</h2>
<blockquote>
  <a href="../../edt_organisation/admin_horaire_ouverture.php?action=visualiser">Définir les horaires d'ouverture de l'établissement</a><br />
  <a href="../../edt_organisation/admin_periodes_absences.php?action=visualiser">Définir les créneaux horaires</a><br />
<?php // On vérifie si le module calendrier / edt est ouvert ou non pour savoir quel lien on lance
$req_setting = mysql_fetch_array(mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_admin'")) OR DIE ('Erreur requête req_setting () : '.mysql_error());
$req_setting2 = mysql_fetch_array(mysql_query("SELECT value FROM setting WHERE name = 'autorise_edt_tous'")) OR DIE ('Erreur requête req_setting2 () : '.mysql_error());
	if ($req_setting["value"] == 'y' OR $req_setting2["value"] == 'y') {
		// On initialise le $_SESSION["retour"] pour pouvoir revenir proprement
		$_SESSION["retour"] = "../mod_absences/admin/index";
		echo '<a href="../../edt_organisation/edt_calendrier.php">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</a><br />';
	} else {
		echo '<a href="admin_config_calendrier.php?action=visualiser">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</a><br />';
	}
?>
	<a href="../../edt_organisation/admin_config_semaines.php?action=visualiser">Définir les types de semaine</a><br />
	<a href="admin_motifs_absences.php?action=visualiser">Définir les motifs des absences</a><br />
	<a href="admin_actions_absences.php?action=visualiser">Définir les actions sur le suivi des élèves</a>
</blockquote>
<?PHP
require("../../lib/footer.inc.php");
 *
 */
?>
