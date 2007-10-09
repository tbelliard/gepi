<?php
// Fichier utilisé par l'administrateur pour paramétrer l'EdT de Gepi

$titre_page = "Emploi du temps - Paramètres";
$affiche_connexion = 'yes';
$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions edt
require_once("./fonctions_edt.php");

// Resume session
$resultat_session = resumeSession();
if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

// Sécurité
if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}
// CSS particulier à l'EdT
$style_specifique = "edt_organisation/style_edt";

// On insère l'entête de Gepi
require_once("../lib/header.inc");

// On ajoute le menu EdT
require_once("./menu.inc.php"); ?>


<br />
<!-- la page du corps de l'EdT -->

	<div id="lecorps">
<center>
<?php

// Initialiser les variables
$edt_aff_matiere=isset($_POST['edt_aff_matiere']) ? $_POST['edt_aff_matiere'] : NULL;
$edt_aff_creneaux=isset($_POST['edt_aff_creneaux']) ? $_POST['edt_aff_creneaux'] : NULL;
$edt_aff_couleur=isset($_POST['edt_aff_couleur']) ? $_POST['edt_aff_couleur'] : NULL;
$edt_aff_salle=isset($_POST['edt_aff_salle']) ? $_POST['edt_aff_salle'] : NULL;
$aff_cherche_salle = isset($_POST["aff_cherche_salle"]) ? $_POST["aff_cherche_salle"] : NULL;
$parametrer=isset($_POST['parametrer']) ? $_POST['parametrer'] : NULL;
$parametrer_ok=isset($_POST['parametrer1']) ? $_POST['parametrer1'] : NULL;

// Récupérer les paramètres tels qu'ils sont déjà définis
if (isset($parametrer_ok)) {

	// Le réglage de l'affichage des matières
	$req_reg_mat = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_matiere'");
	$tab_reg_mat = mysql_fetch_array($req_reg_mat);

	if ($edt_aff_matiere === $tab_reg_mat['valeur']) {
		echo "<span class=\"accept\">Aucune modification de l'affichage des matières</span><br />\n";
	}
	else {
		$modif_aff_mat = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_matiere' WHERE reglage = 'edt_aff_matiere'");
		echo "<span class=\"refus\"> Modification de l'affichage des matières enregistrée</span>\n<br />\n";
	}

	// Le réglage de l'affichage du type d'heure
	$req_reg_cre = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_creneaux'");
	$tab_reg_cre = mysql_fetch_array($req_reg_cre);

	if ($edt_aff_creneaux === $tab_reg_cre['valeur']) {
		echo "<span class=\"accept\">Aucune modification de l'affichage des créneaux</span><br />\n";
	}
	else {
		$modif_aff_cre = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_creneaux' WHERE reglage = 'edt_aff_creneaux'");
		echo "<span class=\"refus\"> Modification de l'affichage des creneaux enregistrée</span>\n<br />\n";
	}

	// Le réglage de l'affichage des couleurs
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur === $tab_reg_coul['valeur']) {
		echo "<span class=\"accept\">Aucune modification des couleurs</span><br />\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur' WHERE reglage = 'edt_aff_couleur'");
		echo "<span class=\"refus\"> Modification de l'affichage des couleurs enregistrée</span>\n<br />\n";
	}

	//Le réglage de l'affichage des salles
	$req_reg_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_salle'");
	$tab_reg_salle = mysql_fetch_array($req_reg_salle);

	if ($edt_aff_salle === $tab_reg_salle['valeur']) {
		echo "<span class=\"accept\">Aucune modification de l'affichage des salles</span><br />\n";
	}
	else {
		$modif_aff_salle = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_salle' WHERE reglage = 'edt_aff_salle'");
		echo "<span class=\"refus\"> Modification de l'affichage des salle enregistrée</span>\n<br />\n";

	}

	// le réglage de l'affichage du menu CHERCHER
	$req_cherche_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'aff_cherche_salle'");
	$rep_cherche_salle = mysql_fetch_array($req_cherche_salle);

	if ($aff_cherche_salle === $rep_cherche_salle["valeur"]) {
		echo "<span class=\"accept\">Aucune modification de l'affichage du menu CHERCHER</span>\n<br />\n";
	}
	else {
		$modif_cherch_salle = mysql_query("UPDATE edt_setting SET valeur = '$aff_cherche_salle' WHERE reglage = 'aff_cherche_salle'");
		echo "<span class=\"refus\">Modification de l'affichage du menu CHERCHER enregistrés</span>\n<br />\n";
	}
} //if (isset($parametrer_ok))
else {
	echo "Dans cette page, vous pouvez paramétrer l'affichage des emplois du temps pour tous les utilisateurs de Gepi.";
}
?>
</center>
<form name="parametrer" method="post" action="edt_parametrer.php">
<table cellpadding="5" cellspacing="0" border="0" style="height: 150px; width: 100%;">
<tr><td>

<fieldset id="matiere">
	<legend>Les matières</legend>
		<span class="parametres">
			<input type="radio" name="edt_aff_matiere" value="court" <?php echo (aff_checked("edt_aff_matiere", "court")); ?>/>
			Noms courts (du type HG,...)
<br />
			<input type="radio" name="edt_aff_matiere" value="long" <?php echo (aff_checked("edt_aff_matiere", "long")); ?>/>
			Noms longs (Histoire Géographie,...)

		</span>
</fieldset>

</td><td>
<fieldset id="horaires">
	<legend>Affichage des horaires</legend>
		<span class="parametres">
			<input type="radio" name="edt_aff_creneaux" value="noms" <?php echo (aff_checked("edt_aff_creneaux", "noms")); ?>/>
			Afficher le nom des cr&eacute;neaux (M1, M2,...)
<br />
			<input type="radio" name="edt_aff_creneaux" value="heures" <?php echo (aff_checked("edt_aff_creneaux", "heures")); ?>/>
			Afficher les heures de d&eacute;but et de fin du cr&eacute;neaux
		</span>
</fieldset>

</td></tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" style="height: 150px; width: 100%;">
<tr><td>
<fieldset id="couleurs">
	<legend>Affichage général en couleur</legend>
		<span class="parametres">
			<input type="radio" name="edt_aff_couleur" value="coul" <?php echo (aff_checked("edt_aff_couleur", "coul")); ?>/>
			Couleurs
<br />
			<input type="radio" name="edt_aff_couleur" value="nb" <?php echo (aff_checked("edt_aff_couleur", "nb")); ?>/>
			Sans couleurs
		</span>
</fieldset>

</td><td>
<fieldset id="salles">
	<legend>Affichage des salles</legend>
		<span class="parametres">
			<input type="radio" name="edt_aff_salle" value="nom" <?php echo (aff_checked("edt_aff_salle", "nom")); ?>/>
			Par le nom de la salle (salle 2, salle de r&eacute;union,...)
<br />
			<input type="radio" name="edt_aff_salle" value="numero" <?php echo (aff_checked("edt_aff_salle", "numero")); ?>/>
			Par le num&eacute;ro de la salle uniquement
		</span>
</fieldset>
</td></tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" style="height: 150px; width: 100%;">
	<tr>
		<td>
<fieldset id="aff_cherche_salle">
	<legend>Fonction chercher les salles vides</legend>
		<span class="parametres">
			<input type="radio" name="aff_cherche_salle" value="admin" <?php echo (aff_checked("aff_cherche_salle", "admin")); ?>/>
			Seul l'administrateur a acc&egrave;s &agrave; cette fonctionnalit&eacute;.
<br />
			<input type="radio" name="aff_cherche_salle" value="tous" <?php echo (aff_checked("aff_cherche_salle", "tous")); ?>/>
			Tous les utilisateurs ont acc&egrave;s &agrave; cette fonctionnalit&eacute; sauf les &eacute;l&egrave;ves et les responsables d'&eacute;l&egrave;ves.
		</span>
</fieldset>
		</td>
		<td></td>
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
