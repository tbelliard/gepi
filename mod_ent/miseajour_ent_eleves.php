<?php
/**
 * @version $Id$
 *
 * Module d'intégration de Gepi dans un ENT réalisé au moment de l'intégration de Gepi dans ARGOS dans l'académie de Bordeaux
 * Fichier permettant de récupérer de nouveaux élèves dans le ldap de l'ENT
 *
 * Copyright 2001, 2008 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane boireau, Julien Jocal
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
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

// Sécurité supplémentaire pour éviter d'aller voir ce fichier si on n'est pas dans un ent
if (getSettingValue("use_ent") != 'y') {
	DIE('Fichier interdit.');
}

// ======================= Initialisation des variables ======================= //
$aff_liste_eleves = NULL;
$enregistrer = isset($_POST['enregistrer']) ? $_POST['enregistrer'] : NULL;
$maj = isset($_POST['maj']) ? $_POST['maj'] : NULL;

// ======================= code métier ======================================== //

// Si c'est demandé, on traite les nouveaux logins
if ($enregistrer == "Ajouter ces élèves") {
	$nbre_a_traiter = count($maj);
	echo $nbre_a_traiter;
}


// On récupère la liste des élèves déjà inscrits dans la base
$sql_all = "SELECT DISTINCT login FROM eleves";
$query_all = mysql_query($sql_all);
$tab_all = array();
while($rep_all = mysql_fetch_array($query_all)){
	$tab_all[] = $rep_all['login'];
} // while

// Puis la liste des élèves présents dans la table ldap_bx
$sql_ent = "SELECT DISTINCT login_u FROM ldap_bx WHERE statut_u = 'student'";
$query_ent = mysql_query($sql_ent);
$tab_ent = array();
while($rep_ent = mysql_fetch_array($query_ent)){
	$tab_ent[] = $rep_ent['login_u'];
} // while

// Et enfin, on compare les deux tableaux et on garde les nouveaux logins
$tab_new_eleves = array_diff($tab_ent, $tab_all);

$test_new = count($tab_new_eleves);
if ($test_new >= 1) {
	// Alors il y a au moins un nouvel élève dans le ldap
	foreach($tab_new_eleves as $rep){

		$aff_liste_eleves .= $rep . '<br />';

	}

}


// =========== fichiers spéciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Les utilisateurs de l'ENT";
require_once("../lib/header.inc");
//**************** FIN EN-TETE *****************
debug_var(); // à enlever en production
?>
<p><a href="../accueil.php">RETOUR vers l'accueil</a></p>
<p>Avant de continuer, vous devez r&eacute;cup&eacute;rer tous les utilisateurs actuellement dans l'ENT <a href="index.php">ICI</a>.</p>

	<form method="post" action="miseajour_ent_eleves.php">
<table>

	<tr>
		<th>Mise &agrave; jour</th>
		<th>Login</th>
	</tr>
		<?php foreach($tab_new_eleves as $rep): ?>
	<tr>
		<td><input type="checkbox" name="maj[]" value="<?php echo $rep; ?>" checked="checked" /></td>
		<td><?php echo $rep; ?></td>
	</tr>
		<?php endforeach; ?>

</table>
		<input type="submit" name="enregistrer" value="Ajouter ces &eacute;l&egrave;ves" />
	</form>

<?php require_once("../lib/footer.inc.php");