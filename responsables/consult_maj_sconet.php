<?php
/*
 *
 * Copyright 2001-2013 Thomas Belliard, Laurent Delineau, Eric Lebrun, Stephane Boireau
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

if(($_SESSION['statut']=='scolarite')&&(!getSettingAOui('GepiAccesMajSconetScol'))) {
	header("Location: ../accueil.php?msg=Mise à jour Sconet non autorisée en compte scolarité.");
	die();
}

if(isset($_POST['suppr'])) {
	check_token();

	$msg="";
	$suppr=$_POST['suppr'];

	$cpt_suppr=0;
	for($i=0;$i<count($suppr);$i++) {
		$sql="DELETE FROM log_maj_sconet WHERE id='".$suppr[$i]."';";
		$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
		if(!$res) {
			$msg.="Erreur lors de la suppression du compte-rendu n°".$suppr[$i]."<br />";
		}
		else {
			$cpt_suppr++;
		}
	}

	if($cpt_suppr>0) {
		$msg.="Suppression de $cpt_suppr compte-rendu(s).<br />";
	}
}

//**************** EN-TETE *****************
$titre_page = "Consultation Màj Sconet";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
echo "<p class='bold'>
<a href=\"maj_import.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

echo "<h2>Mises à jour d'après Sconet/Siècle</h2>";

$sql="SELECT * FROM log_maj_sconet ORDER BY date_debut;";
$res=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
if(mysqli_num_rows($res)==0){
	echo "<p>Aucun compte-rendu de mise à jour d'après Sconet n'est enregistré dans la table 'log_maj_sconet'.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

echo "<form action='".$_SERVER['PHP_SELF']."' method='post' />
	".add_token_field()."
	<p>Voici les compte-rendus enregistrés&nbsp;:</p>
	<table class='boireaus' summary=\"Tableau des Mises à jour d'après Sconet\">
		<thead>
			<tr>
				<th colspan='2'>Date de</th>
				<th rowspan='2'>Effectuée<br />par</th>
				<th rowspan='2'>Compte-rendu</th>
				<th rowspan='2'>
					<input type='submit' value='Supprimer' /><br />
					<a href='javascript:CocheColonne();changement();'><img src='../images/enabled.png' width='15' height='15' alt='Tout cocher' title='Tout cocher' /></a> / <a href='javascript:DecocheColonne();changement();'><img src='../images/disabled.png' width='15' height='15' alt='Tout décocher' title='Tout décocher' /></a>
				</th>
			</tr>
			<tr>
				<th>début</th>
				<th>fin</th>
			</tr>
		</thead>
		<tbody>";
$cpt=0;
$alt=1;
while($lig=mysqli_fetch_object($res)) {
	$alt=$alt*(-1);
	echo "
			<tr class='lig$alt white_hover'>
				<td>".formate_date($lig->date_debut, 'y')."</td>
				<td>".(($lig->date_fin!="0000-00-00 00:00:00") ? formate_date($lig->date_fin, 'y') : "")."</td>
				<td>".civ_nom_prenom($lig->login)."</td>
				<td>".$lig->texte."</td>
				<td><input type='checkbox' name='suppr[]' id='suppr_$cpt' value='".$lig->id."' /></td>
			</tr>";
	$cpt++;
}
	echo "
		</tbody>
	</table>
</form>

<script type='text/javascript'>

	function CocheColonne() {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('suppr_'+ki)){
				document.getElementById('suppr_'+ki).checked = true;
			}
		}
	}

	function DecocheColonne() {
		for (var ki=0;ki<$cpt;ki++) {
			if(document.getElementById('suppr_'+ki)){
				document.getElementById('suppr_'+ki).checked = false;
			}
		}
	}
</script>

<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
