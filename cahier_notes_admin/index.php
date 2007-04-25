<?php
/*
 * Last modification  : 04/04/2005
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
// Resume session
$resultat_session = resumeSession();
if ($resultat_session == 'c') {
header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
die();
};

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
die();
}
$msg = '';
if (isset($_POST['activer'])) {
    if (!saveSetting("active_carnets_notes", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
}


if(isset($_POST['is_posted'])){
	if (isset($_POST['export_cn_ods'])) {
		//if (!saveSetting("export_cn_ods", $_POST['export_cn_ods'])) {
		if (!saveSetting("export_cn_ods", 'y')) {
			$msg .= "Erreur lors de l'enregistrement de l'autorisation de l'export au format ODS !";
		}
	}
	else{
		if (!saveSetting("export_cn_ods", 'n')) {
			$msg .= "Erreur lors de l'enregistrement de l'interdiction de l'export au format ODS !";
		}
	}
}


if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées !";
// header
$titre_page = "Gestion des carnets de notes";
require_once("../lib/header.inc");
?>
<p class=bold><a href="../accueil_modules.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<h2>Configuration générale</h2>
<i>La désactivation des carnets de notes n'entraîne aucune suppression des données. Lorsque le module est désactivé, les professeurs n'ont pas accès au module.</i>
<br />
<form action="index.php" name="form1" method="post">

<p>
<input type="radio" name="activer" value="y" <?php if (getSettingValue("active_carnets_notes")=='y') echo " checked"; ?> />&nbsp;Activer les carnets de notes<br />
<input type="radio" name="activer" value="n" <?php if (getSettingValue("active_carnets_notes")=='n') echo " checked"; ?> />&nbsp;Désactiver les carnets de notes
</p>

<?php
	echo "<p>\n";
	if(file_exists("../lib/ss_zip.class.php")){
		echo "<input type='checkbox' name='export_cn_ods' value='y'";
		if(getSettingValue('export_cn_ods')=='y'){
			echo ' checked';
		}
		echo " /> \n";
		echo "Permettre l'export des carnets de notes au format ODS.<br />(<i>si les professeurs ne font pas le ménage après génération des exports,<br />ces fichiers peuvent prendre de la place sur le serveur</i>)\n";
	}
	else{
		echo "En mettant en place la bibliothèque 'ss_zip_.class.php' dans le dossier '/lib/', vous pouvez générer des fichiers tableur ODS pour permettre des saisies hors ligne, la conservation de données,...<br />Voir <a href='http://smiledsoft.com/demos/phpzip/' target='_blank'>http://smiledsoft.com/demos/phpzip/</a><br />Une version limitée est disponible gratuitement.\n";

		// Comme la bibliothèque n'est pas présente, on force la valeur à 'n':
		$svg_param=saveSetting("export_cn_ods", 'n');
	}
	echo "</p>\n";
?>


<input type="hidden" name="is_posted" value="1" />
<center><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></center>
</form>
<?php require("../lib/footer.inc.php");?>