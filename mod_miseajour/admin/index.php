<?php
/*
 * Last modification  : 03/12/2006
 *
 * Copyright 2001, 2002 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Christian Chapel
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

$niveau_arbo = 2;
// Initialisations files
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

$msg = '';
if (isset($_POST['activer'])) {
    if (!saveSetting("active_module_msj", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}
if (isset($_POST['site_msj_gepi'])) {
    if (!saveSetting("site_msj_gepi", $_POST['site_msj_gepi'])) $msg = "Erreur lors de l'enregistrement de l'adresse du site de mise à jour !";
}
if (isset($_POST['dossier_ftp_gepi'])) {
    if (!saveSetting("dossier_ftp_gepi", $_POST['dossier_ftp_gepi'])) $msg = "Erreur lors de l'enregistrement du nom du dossier d'installation de gepi sur FTP !";
}
if (isset($_POST['activer_rc'])) {
    if (!saveSetting("rc_module_msj", $_POST['activer_rc'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation des RCs!";
}
if (isset($_POST['activer_beta'])) {
    if (!saveSetting("beta_module_msj", $_POST['activer_beta'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation des BETAs!";
}

if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";

// ============= header ==============
	// Inclusion du style spécifique
$style_specifique = "/mod_miseajour/lib/style_maj";

$titre_page = "Gestion du module de mise à jour";
require_once("../../lib/header.inc");
// ============= fin header ==========
?>
<p class="bold">
|<a href="../../accueil.php">Accueil</a>|
<a href="../../accueil_modules.php">Retour administration des modules</a>|
</p>
<h2>Gestion des mise à jour de GEPI</h2>
<p><i>La désactivation du module de la gestion des mises à jour n'entraîne aucune suppression des données. Lorsque le module est désactivé, les administrateurs n'ont pas accès au module.</i></p>
<p>Note : l'option 'allow_url_fopen' dans php.ini doit être à 'On' sur le serveur pour que ce module puisse fonctionner.</p>
<br />
<form action="index.php" name="form1" method="post">
	<p>
	<input type="radio" id="activMaj" name="activer" value="y" <?php if (getSettingValue("active_module_msj")=='y') echo ' checked="checked"'; ?> />
	<label for="activMaj">&nbsp;Activer le module de mise à jour de GEPI</label>
	</p>
	<p>
	<input type="radio" id="desactiMaj" name="activer" value="n" <?php if (getSettingValue("active_module_msj")=='n') echo ' checked="checked"'; ?> />
	<label for="desactiMaj">&nbsp;Désactiver le module de mise à jour de GEPI</label>
	</p>
<br />
	<p>Par d&eacute;faut, seules les versions stables sont v&eacute;rifi&eacute;es, mais vous pouvez inclure les autres versions.</p>
	<p class="decale">
	Afficher les versions RC&nbsp;<a class="info" style="font-weight: bold;">?
		<span style="width: 400px;">Attention les version RC sont des versions de test donc à ne jamais utiliser en production.</span></a>
		<input type="radio" id="activRc" name="activer_rc" value="y" <?php if (getSettingValue("rc_module_msj")=='y') echo ' checked="checked"'; ?> />
		<label for="actiRc">Activer</label>
		<input type="radio" id="desactivRc" name="activer_rc" value="n" <?php if (getSettingValue("rc_module_msj")=='n') echo ' checked="checked"'; ?> />
		<label for="desactivRc">Désactiver</label>
	</p>
	<p class="decale">
	Afficher les versions BETA&nbsp;<a class="info" style="font-weight: bold;">?
		<span style="width: 400px;">Attention les version BETA sont des versions de développement donc à ne jamais utiliser en production.</span></a>
		<input type="radio" id="activBeta" name="activer_beta" value="y" <?php if (getSettingValue("beta_module_msj")=='y') echo ' checked="checked"'; ?> />
		<label for="activBeta">Activer</label>
		<input type="radio" id="desactivBeta" name="activer_beta" value="n" <?php if (getSettingValue("beta_module_msj")=='n') echo ' checked="checked"'; ?> />
		<label for="desactivBeta">Désactiver</label>
	</p>

<h2>Information site de mise à jour de GEPI</h2>
	<p class="decale">
	<label for="siteMaj">Adresse du site internet de mise à jour de GEPI</label>
	<input type="text" id="siteMaj" name="site_msj_gepi" value="<?php echo getSettingValue("site_msj_gepi"); ?>" size="40" />
	</p>

<h2>Information serveur FTP</h2>
	<p class="decale">
	<label for="dossierFtp">Nom du dossier d'installation de GEPI sur le FTP utilisé</label>
	<input type="text" id="dossierFtp" name="dossier_ftp_gepi" value="<?php echo getSettingValue("dossier_ftp_gepi"); ?>" size="20" />&nbsp; ex: gepi
	</p>
	<p class="decale">
	<input type="hidden" name="is_posted" value="1" />
	<input type="submit" value="Enregistrer" style="font-variant: small-caps;" />
	</p>
</form>

<br />

<?php
require_once("../../lib/footer.inc.php");
?>

