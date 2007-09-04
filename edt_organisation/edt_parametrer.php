<?php
// Fichier utilisé par l'administrateur pour paramétrer l'EdT de Gepi
/*
DROP TABLE IF EXISTS `edt_setting`;
CREATE TABLE `edt_setting` (
  `id` int(3) NOT NULL auto_increment,
  `reglage` varchar(30) collate latin1_general_ci NOT NULL,
  `valeur` varchar(30) collate latin1_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

--
-- Contenu de la table `edt_setting`
--

INSERT INTO `edt_setting` (`id`, `reglage`, `valeur`) VALUES
(1, 'nom_creneaux_s', '1'),
(2, 'edt_aff_salle', 'nom'),
(3, 'edt_aff_matiere', 'court'),
(4, 'edt_aff_creneaux', 'heures'),
(5, 'edt_aff_couleur', 'nb');

*/

// Initialiser les variables
$edt_aff_matiere=isset($_POST['edt_aff_matiere']) ? $_POST['edt_aff_matiere'] : NULL;
$edt_aff_creneaux=isset($_POST['edt_aff_creneaux']) ? $_POST['edt_aff_creneaux'] : NULL;
$edt_aff_couleur=isset($_POST['edt_aff_couleur']) ? $_POST['edt_aff_couleur'] : NULL;
$edt_aff_salle=isset($_POST['edt_aff_salle']) ? $_POST['edt_aff_salle'] : NULL;
$parametrer=isset($_POST['parametrer']) ? $_POST['parametrer'] : NULL;
$parametrer_ok=isset($_POST['parametrer1']) ? $_POST['parametrer1'] : NULL;

// Récupérer les paramètres tels qu'ils sont déjà définis
if (isset($parametrer_ok)) {

	echo "<font size=\"2\">\n";

	// Le réglage de l'affichage des matières
	$req_reg_mat = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_matiere'");
	$tab_reg_mat = mysql_fetch_array($req_reg_mat);

	if ($edt_aff_matiere === $tab_reg_mat['valeur']) {
		echo "<font color=\"green\">Aucune modification de l'affichage des matières</font><br />\n";
	}
	else {
		$modif_aff_mat = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_matiere' WHERE reglage = 'edt_aff_matiere'");
		echo "<font color=\"red\"> Modification de l'affichage des matières enregistrée</font>\n<br />\n";
	}

	// Le réglage de l'affichage du type d'heure
	$req_reg_cre = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_creneaux'");
	$tab_reg_cre = mysql_fetch_array($req_reg_cre);

	if ($edt_aff_creneaux === $tab_reg_cre['valeur']) {
		echo "<font color=\"green\">Aucune modification de l'affichage des créneaux</font><br />\n";
	}
	else {
		$modif_aff_cre = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_creneaux' WHERE reglage = 'edt_aff_creneaux'");
		echo "<font color=\"red\"> Modification de l'affichage des creneaux enregistrée</font>\n<br />\n";
	}

	// Le réglage de l'affichage des couleurs
	$req_reg_coul = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_couleur'");
	$tab_reg_coul = mysql_fetch_array($req_reg_coul);

	if ($edt_aff_couleur === $tab_reg_coul['valeur']) {
		echo "<font color=\"green\">Aucune modification des couleurs</font><br />\n";
	}
	else {
		$modif_aff_coul = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_couleur' WHERE reglage = 'edt_aff_couleur'");
		echo "<font color=\"red\"> Modification de l'affichage des couleurs enregistrée</font>\n<br />\n";
	}

	//Le réglage de l'affichage des salles
	$req_reg_salle = mysql_query("SELECT valeur FROM edt_setting WHERE reglage = 'edt_aff_salle'");
	$tab_reg_salle = mysql_fetch_array($req_reg_salle);

	if ($edt_aff_salle === $tab_reg_salle['valeur']) {
		echo "<font color=\"green\">Aucune modification de l'affichage des salles</font><br />\n";
	}
	else {
		$modif_aff_salle = mysql_query("UPDATE edt_setting SET valeur = '$edt_aff_salle' WHERE reglage = 'edt_aff_salle'");
		echo "<font color=\"red\"> Modification de l'affichage des salle enregistrée</font>\n<br />\n";

	}
		echo "</font>\n";
}
?>
<?php if ($parametrer_ok != "ok") {
	echo "Dans cette page, vous pouvez paramétrer l'affichage des emplois du temps pour tous les utilisateurs de Gepi.";
}
?>
</center>
<table cellpadding="5" cellspacing="0" border="0" height="150" width="100%">
<tr><td>
<form method=post action="index_edt.php">

<fieldset id="matiere">
	<legend>Les matières</legend>
		<font size="2">
			<INPUT type="radio" name="edt_aff_matiere" value="court" <?php echo (aff_checked("edt_aff_matiere", "court")); ?>/>
			Noms courts (du type HG,...)
<br />
			<INPUT type="radio" name="edt_aff_matiere" value="long" <?php echo (aff_checked("edt_aff_matiere", "long")); ?>/>
			Noms longs (Histoire Géographie,...)

		</font>
</fieldset>

</td><td>
<fieldset id="horaires">
	<legend>Affichage des horaires</legend>
		<font size="2">
			<INPUT type="radio" name="edt_aff_creneaux" value="noms" <?php echo (aff_checked("edt_aff_creneaux", "noms")); ?>/>
			Afficher le nom des cr&eacute;neaux (M1, M2,...)
<br />
			<INPUT type="radio" name="edt_aff_creneaux" value="heures" <?php echo (aff_checked("edt_aff_creneaux", "heures")); ?>/>
			Afficher les heures de d&eacute;but et de fin du cr&eacute;neaux
		</font>
</fieldset>

</td></tr>
</table>

<table cellpadding="5" cellspacing="0" border="0" height="150" width="100%">
<tr><td>
<fieldset id="couleurs">
	<legend>Affichage général en couleur</legend>
		<font size="2">
			<INPUT type="radio" name="edt_aff_couleur" value="coul" <?php echo (aff_checked("edt_aff_couleur", "coul")); ?>/>
			Couleurs
<br />
			<INPUT type="radio" name="edt_aff_couleur" value="nb" <?php echo (aff_checked("edt_aff_couleur", "nb")); ?>/>
			Sans couleurs
		</font>
</fieldset>

</td><td>
<fieldset id="salles">
	<legend>Affichage des salles</legend>
		<font size="2">
			<input type="radio" name="edt_aff_salle" value="nom" <?php echo (aff_checked("edt_aff_salle", "nom")); ?> />
			Par le nom de la salle (salle 2, salle de r&eacute;union,...)
<br />
			<input type="radio" name="edt_aff_salle" value="numero" <?php echo (aff_checked("edt_aff_salle", "numero")); ?> />
			Par le num&eacute;ro de la salle uniquement
		</font>
</fieldset>
</td></tr>
</table>
<center>
	<INPUT type="hidden" name="parametrer" value="ok">
	<INPUT type="hidden" name="parametrer1" value="ok">
	<INPUT type="submit" name="Valider" value="Valider">

</form>
