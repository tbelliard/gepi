<?php
/**
 *
 * @version $Id$
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal
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

// Fichier utilisé par l'administrateur pour paramétrer l'EdT de Gepi
require_once("./choix_langue.php");

$titre_page = TITLE_EDT_PARAMETRER;
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// Sécurité supplémentaire par rapport aux paramètres du module EdT / Calendrier
if (param_edt($_SESSION["statut"]) != "yes") {
	Die(ASK_AUTHORIZATION_TO_ADMIN);
}
// CSS et js particulier à l'EdT
$javascript_specifique = "edt_organisation/script/fonctions_edt";
$style_specifique = "templates/".NameTemplateEDT()."/css/style_edt";
//=========Utilisation de prototype et des js de base ===========
$utilisation_prototype = "";
$utilisation_jsbase = "";
//=========Fin des Prototype et autres js =======================

// Initialiser les variables
$edt_aff_matiere=isset($_POST['edt_aff_matiere']) ? $_POST['edt_aff_matiere'] : NULL;
$edt_aff_creneaux=isset($_POST['edt_aff_creneaux']) ? $_POST['edt_aff_creneaux'] : NULL;
$edt_aff_couleur=isset($_POST['edt_aff_couleur']) ? $_POST['edt_aff_couleur'] : NULL;
$edt_aff_salle=isset($_POST['edt_aff_salle']) ? $_POST['edt_aff_salle'] : NULL;
$aff_cherche_salle = isset($_POST["aff_cherche_salle"]) ? $_POST["aff_cherche_salle"] : NULL;
$parametrer=isset($_POST['parametrer']) ? $_POST['parametrer'] : NULL;
$parametrer_ok=isset($_POST['parametrer1']) ? $_POST['parametrer1'] : NULL;
$param_menu_edt = isset($_POST["param_menu_edt"]) ? $_POST["param_menu_edt"] : NULL;


// Récupérer les paramètres tels qu'ils sont déjà définis
if (isset($parametrer_ok)) {
	$aff_message = "";
	// Le réglage de l'affichage des matières
	$req_reg_mat = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_matiere'");
	$tab_reg_mat = mysql_fetch_array($req_reg_mat);

	if ($edt_aff_matiere === $tab_reg_mat['valeur']) {
		$aff_message .= "<p class=\"accept\">Aucune modification de l'affichage des matières</p>\n";
	}
	else {
		$modif_aff_mat = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_matiere' WHERE reglage = 'edt_aff_matiere'");
		$aff_message .= "<p class=\"refus\"> Modification de l'affichage des matières enregistrée</p>\n";
	}

	// Le réglage de l'affichage du type d'heure
	$req_reg_cre = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_creneaux'");
	$tab_reg_cre = mysql_fetch_array($req_reg_cre);

	if ($edt_aff_creneaux === $tab_reg_cre['valeur']) {
		$aff_message .= "<p class=\"accept\">Aucune modification de l'affichage des créneaux</p>\n";
	}
	else {
		$modif_aff_cre = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_creneaux' WHERE reglage = 'edt_aff_creneaux'");
		$aff_message .= "<p class=\"refus\"> Modification de l'affichage des créneaux enregistrée</p>\n";
	}

	// Le réglage de l'affichage des couleurs
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur === $tab_reg_coul['valeur']) {
		$aff_message .= "<p class=\"accept\">Aucune modification des couleurs</p>\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur' WHERE reglage = 'edt_aff_couleur'");
		$aff_message .= "<p class=\"refus\"> Modification de l'affichage des couleurs enregistrée</p>\n";
	}

	//Le réglage de l'affichage des salles
	$req_reg_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_salle'");
	$tab_reg_salle = mysql_fetch_array($req_reg_salle);

	if ($edt_aff_salle === $tab_reg_salle['valeur']) {
		$aff_message .= "<p class=\"accept\">Aucune modification de l'affichage des salles</p>\n";
	}
	else {
		$modif_aff_salle = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_salle' WHERE reglage = 'edt_aff_salle'");
		$aff_message .= "<p class=\"refus\"> Modification de l'affichage des salle enregistrée</p>\n";

	}

	// le réglage de l'affichage du menu CHERCHER
	$req_cherche_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'aff_cherche_salle'");
	$rep_cherche_salle = mysql_fetch_array($req_cherche_salle);

	if ($aff_cherche_salle === $rep_cherche_salle["valeur"]) {
		$aff_message .= "<p class=\"accept\">Aucune modification de l'affichage du menu CHERCHER</p>\n";
	}
	else {
		$modif_cherch_salle = mysql_query("UPDATE edt_setting SET valeur = '$aff_cherche_salle' WHERE reglage = 'aff_cherche_salle'");
		$aff_message .= "<p class=\"refus\">Modification de l'affichage du menu CHERCHER enregistrée</p>\n";
	}

	// Le réglage du fonctionnement du menu (param_menu_edt)
	$req_param_menu = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'param_menu_edt'");
	$rep_param_menu = mysql_fetch_array($req_param_menu);

	if ($param_menu_edt === $rep_param_menu["valeur"]) {
		$aff_message .= "<p class=\"accept\">Aucune modification du fonctionnement du menu.</p>\n";
	} else {
		$modif_param_menu = mysql_query("UPDATE edt_setting SET valeur = '$param_menu_edt' WHERE reglage = 'param_menu_edt'");
		$aff_message .= "<p class=\"refus\">Modification du fonctionnement du menu enregistrée.</p>\n";
	}

} //if (isset($parametrer_ok))
else {
	$message = "Dans cette page, vous pouvez paramétrer l'affichage des emplois du temps pour tous les utilisateurs de Gepi.";
}

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php");
?>

<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<?php
if (isset($aff_message)) {
	echo $aff_message;
}
?>
<form name="parametrer" method="post" action="edt_parametrer.php">
<table cellpadding="5" cellspacing="0" border="0" summary="Parametres" style="height: 150px; width: 100%;">
<tr><td>

<fieldset id="matiere">
	<legend><?php echo FIELDS_PARAM ?></legend>
		<p>
			<input type="radio" id="edtMatiereCourt" name="edt_aff_matiere" value="court" <?php echo (aff_checked("edt_aff_matiere", "court")); ?>/>
			<label for="edtMatiereCourt"><?php echo FIELDS_PARAM_BUTTON1 ?></label>
<br />
			<input type="radio" id="edtMatiereLong" name="edt_aff_matiere" value="long" <?php echo (aff_checked("edt_aff_matiere", "long")); ?>/>
			<label for="edtMatiereLong"><?php echo FIELDS_PARAM_BUTTON2 ?></label>

		</p>
</fieldset>

</td><td>
<fieldset id="horaires">
	<legend>Affichage des horaires</legend>
		<p>
			<input type="radio" id="edtCreneauxNoms" name="edt_aff_creneaux" value="noms" <?php echo (aff_checked("edt_aff_creneaux", "noms")); ?>/>
			<label for="edtCreneauxNoms">Afficher le nom des cr&eacute;neaux (M1, M2,...).</label>
<br />
			<input type="radio" id="edtCreneauxHeures" name="edt_aff_creneaux" value="heures" <?php echo (aff_checked("edt_aff_creneaux", "heures")); ?>/>
			<label for="edtCreneauxHeures">Afficher les heures de d&eacute;but et de fin du cr&eacute;neau.</label>
		</p>
</fieldset>

</td></tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" style="height: 150px; width: 100%;">
<tr><td>
<fieldset id="couleurs">
	<legend>Affichage général en couleur</legend>
		<p>
			<input type="radio" id="edtAffCouleur" name="edt_aff_couleur" value="coul" <?php echo (aff_checked("edt_aff_couleur", "coul")); ?>/>
			<label for="edtAffCouleur">Couleurs</label>
<br />
			<input type="radio" id="edtAffNb" name="edt_aff_couleur" value="nb" <?php echo (aff_checked("edt_aff_couleur", "nb")); ?>/>
			<label for="edtAffNb">Sans couleur</label>
		</p>
</fieldset>

</td><td>
<fieldset id="salles">
	<legend>Affichage des salles</legend>
		<p>
			<input type="radio" id="affSalleNom" name="edt_aff_salle" value="nom" <?php echo (aff_checked("edt_aff_salle", "nom")); ?>/>
			<label for="affSalleNom">Par le nom de la salle (salle 2, salle de r&eacute;union,...).</label>
<br />
			<input type="radio" id="affSalleNumero" name="edt_aff_salle" value="numero" <?php echo (aff_checked("edt_aff_salle", "numero")); ?>/>
			<label for="affSalleNumero">Par le num&eacute;ro de la salle uniquement.</label>
		</p>
</fieldset>
</td></tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" style="height: 150px; width: 100%;">
	<tr>
		<td>
<fieldset id="aff_cherche_salle">
	<legend>Fonction chercher les salles vides</legend>
		<p>
			<input type="radio" id="affSalleAdmin" name="aff_cherche_salle" value="admin" <?php echo (aff_checked("aff_cherche_salle", "admin")); ?>/>
			<label for="affSalleAdmin"> l'administrateur a acc&egrave;s &agrave; cette fonctionnalit&eacute;.</label>
<br />
			<input type="radio" id="affSalleTous" name="aff_cherche_salle" value="tous" <?php echo (aff_checked("aff_cherche_salle", "tous")); ?>/>
			<label for="affSalleTous"> Tous les utilisateurs ont acc&egrave;s &agrave; cette fonctionnalit&eacute; sauf les &eacute;l&egrave;ves et les responsables d'&eacute;l&egrave;ves.</label>
		</p>
</fieldset>
		</td>
		<td>
<fieldset id="param_edtmenu">
	<legend>Le fonctionnement du menu</legend>
	<p>
		<input type="radio" id="edtMenuOver" name="param_menu_edt" value="mouseover" <?php echo (aff_checked("param_menu_edt", "mouseover")); ?>/>
		<label for="edtMenuOver">Les liens s'affichent quand la souris passe sur le titre.</label>
	</p>

	<p>
		<input type="radio" id="edtMenuClick" name="param_menu_edt" value="click" <?php echo (aff_checked("param_menu_edt", "click")); ?>/>
		<label for="edtMenuClick">Les liens s'affichent quand l'utilisateur clique sur le titre.</label>
	</p>

	<p>
		<input type="radio" id="edtMenuRien" name="param_menu_edt" value="rien" <?php echo (aff_checked("param_menu_edt", "rien")); ?>/>
		<label for="edtMenuRien">Tous les liens sont visibles tout le temps.</label>
	</p>
</fieldset>
		</td>
	</tr>


</table>
	<input type="hidden" name="parametrer" value="ok" />
	<input type="hidden" name="parametrer1" value="ok" />
	<input type="submit" name="Valider" value="Valider" />

</form>
	</div>
<!--Fin du corps de la page-->
<br />
<br />
<?php
// inclusion du footer
require("../lib/footer.inc.php");
?>
