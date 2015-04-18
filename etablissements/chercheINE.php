<?php

/*
 *
 * Copyright 2015 Bouguin Régis
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
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

/*===== TODO : gérer de façon correcte le droit sur la page =====*/
// On crée l'entrée dans la table droits en attendant une gestion des droits plus propre
$sql= "INSERT INTO `gepi`.`droits` "
   . "(`id` ,`administrateur` ,`professeur` ,`cpe` ,`scolarite` ,`eleve` ,`responsable` ,`secours` ,`autre` ,`description` ,`statut`) "
   . "VALUES ('/etablissements/chercheINE.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'Recherche des INE sans établissements', '') "
   . "ON DUPLICATE KEY UPDATE `administrateur`='V'; ";

$resultchargeDB = $mysqli->query($sql);
/*===== FIN TODO : gérer de façon correcte le droit sur la page =====*/

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

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}


//*************** FONCTIONS ***************
function ineSansEtab () {
	global $mysqli;
	$sql= "SELECT DISTINCT id_etablissement "
	   . "FROM j_eleves_etablissements "
	   . "WHERE id_etablissement NOT IN ( "
	   . "SELECT id_etablissement FROM `j_eleves_etablissements` JOIN etablissements ON id_etablissement = id "
	   . ") ORDER BY id_etablissement ASC";
	//echo $sql."<br />";
	$resultchargeDB = $mysqli->query($sql);
	return $resultchargeDB;
}

//*************** Fin FONCTIONS ***************

$etabATraite = ineSansEtab ();

//**************** EN-TETE *****************
$titre_page = "INE sans établissements";
// ====== Inclusion des balises head et du bandeau =====
if (!suivi_ariane($_SERVER['PHP_SELF'],"INE sans établissements"))
		echo "erreur lors de la création du fil d'ariane";

require_once("../lib/header.inc.php");
// include_once("../lib/header_template.inc.php");
//**************** FIN EN-TETE *****************
?>

<?php if ($etabATraite->num_rows) { ?>
<table class='boireaus'>
	<caption style="caption-side:bottom">Identifiants non rattachés à un établissement</caption>
	<tr>
		<th>INE</th>
		<th>nom</th>
		<th>niveau</th>
		<th>type</th>
		<th>cp</th>
		<th>ville</th>		  	 	 	 	 
	</tr>
<?php while($INE = $etabATraite->fetch_object()){ ?>
	<tr>
		<td>
			<?php echo $INE->id_etablissement; ?>
		</td>
		<td>
			
		</td>
		<td>
			
		</td>
		<td>
			
		</td>
		<td>
			
		</td>
		<td>
			
		</td>
	</tr>
<?php } ?>
</table>


<?php } else { ?>
<p class="vert bold center">
	<br />
	Tous le INE sont rattachés à un établissement
</p>
		
<?php } ?>

<?php
require("../lib/footer.inc.php");
//$tbs_microtime	="";
//$tbs_pmv="";
//require_once ("../lib/footer_template.inc.php");
