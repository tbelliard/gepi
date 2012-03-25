<?php

/*
 *
 * Copyright 2001, 2011 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stéphane boireau, Julien Jocal
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
	die('Fichier interdit.');
}

// ======================= Initialisation des variables ==========================
$aff_erreurs = $aff_logins_m = NULL;
$action = isset($_POST["action"]) ? $_POST["action"] : NULL;
$nbre_req = isset($_POST["nbre_req"]) ? $_POST["nbre_req"] : NULL;
//$ = isset($_POST[""]) ? $_POST[""] : NULL;


// ======================= Traitement des données ================================
if ($action == "modifier") {
	check_token();
	// L'utilisateur vient d'envoyer la liste des login à modifier
	echo $nbre_req;

	for($a = 0 ; $a < $nbre_req ; $a++){

		$login_a_modifier = isset($_POST["modifier_".$a]) ? $_POST["modifier_".$a] : NULL;
		$id_col2 = isset($_POST["id_".$a]) ? $_POST["id_".$a] : NULL;

		// On met à jour les deux tables tempo2 et utilisateurs
		$sql1 = "UPDATE tempo2 SET col1 = '".$login_a_modifier."' WHERE col2 = '".$id_col2."'";
		$query1 = mysql_query($sql1) OR DIE('Erreur sur la requête '.$sql1.'<br />'.mysql_error().'<br />Veuillez contacter les services concernés.');

		$sql2 = "UPDATE utilisateurs SET login = '".$login_a_modifier."' WHERE numind = '".$id_col2."'";
		$query1 = mysql_query($sql2) OR DIE('Erreur sur la requête '.$sql2.'<br />'.mysql_error().'<br />Veuillez contacter les services concernés.');

	}

} else {

	// On récupère la liste de tous les professeurs de la table ldap_bx
	$sql_bx = "SELECT * FROM ldap_bx WHERE statut_u = 'teacher' ORDER BY nom_u, prenom_u";
	$query_bx = mysql_query($sql_bx) OR DIE('Erreur dans la requête '.$sql_bx.'<br />'.mysql_error());

	$aff_ldap_bx = '<p>Liste des professeurs disponibles dans l\'ENT</p>
					<p>Pour ajouter des utilisateurs, contactez votre administrateur ENT</p>';

	while($rep = mysql_fetch_array($query_bx)){

		$aff_ldap_bx .= "\n".'<br />'.$rep["nom_u"].' '.$rep["prenom_u"].' ('.$rep["login_u"].')';

	}

	// On récupère le login des profs avec le motif 'erreur'
	$sql_p = "SELECT u.login, u.nom, u.prenom, t.col2 FROM utilisateurs u, tempo2 t
														WHERE u.statut = 'professeur'
														AND u.login = t.col1
														AND u.login LIKE 'erreur_%'
														ORDER BY u.nom, u.prenom";
	$query_p = mysql_query($sql_p)
				OR DIE('Erreur dans la requête '.$sql_p.'<br />'.mysql_error().'<br /> Veuillez en informer le gestionnaire du système');

	$j = 0;

	while($rep_p = mysql_fetch_array($query_p)){

		$aff_erreurs .= '<p> ERREUR ? : login retenu par Gepi -> <input type="text" name="modifier_'.$j.'" value="'.$rep_p["login"].'" />
							<input type="hidden" name="id_'.$j.'" value="'.$rep_p["col2"].'" /> : '.$rep_p["nom"].' '.$rep_p["prenom"].'</p>';
		$j++;
	}
}

// =========== fichiers spéciaux ==========
$style_specifique = "edt_organisation/style_edt";
//**************** EN-TETE *****************
$titre_page = "Gestion des erreurs de login de l'ENT";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
//debug_var(); // à enlever en production
?>

<!-- Gestion des utilisateurs -->
<div>

<div id="droite" style="border: 1px solid grey; position: absolute; margin-left: 800px;">

	<p style="color: brown;">Si l'utilisateur n'est pas pr&eacute;sent dans cette liste, vous devez le cr&eacute;er dans l'ENT ou vous pouvez pr&eacute;cisez son login si vous le connaissez.</p>

	<?php echo $aff_ldap_bx; ?>

</div>

<div id="gauche" style="width: 790px;">

	<p style="color: green;">Vous pouvez modifier les logins retenus par GEPI &agrave; la main.</p>
	<p>M&ecirc;me si l'utilisateur n'existe pas dans l'ENT, vous connaissez la forme de son login (pre.nom ou pnom ,...).</p>
	<p>Il est imp&eacute;ratif que l'utilisateur ait le m&ecirc;me login dans les deux applications (ENT et GEPI)</p>

	<form name="" action="gestion_ent_profs.php" method="post">

		<input type="hidden" name="action" value="modifier" />
		<input type="hidden" name="nbre_req" value="<?php echo $j; ?>" />


	<?php
		echo $aff_erreurs;

		echo add_token_field();
	?>

		<input type="submit" name="enregistrer" value="Enregistrer" />

	</form>
	<br />


</div>

</div>

<p><a href="../init_xml2/prof_disc_classe_csv.php">Continuer l'initialisation</a></p>
<?php
require("../lib/footer.inc.php");
?>