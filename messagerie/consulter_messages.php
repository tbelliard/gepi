<?php
/*
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/messagerie/consulter_messages.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/messagerie/consulter_messages.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Consulter les messages individuels sur le Panneau d affichage',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}


//$id_mess = isset($_POST["id_mess"]) ? $_POST["id_mess"] :(isset($_GET["id_mess"]) ? $_GET["id_mess"] :NULL);

if(isset($_POST['valider_les_suppressions'])) {
	check_token();

	$suppr = isset($_POST["suppr"]) ? $_POST["suppr"] : array();

	$msg='';
	$nb_suppr=0;
	foreach($suppr as $key => $value) {
		$sql="DELETE FROM messages WHERE id='".$value."';";
		$del=mysqli_query($mysqli, $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression du message n°$value.<br />";
		}
		else {
			$nb_suppr++;
		}
	}

	if($nb_suppr>0) {
		$msg.=$nb_suppr." suppression(s) effectuée(s).<br />";
	}
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Messages individuels";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

echo "<p class='bold'>
	<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour
	</a>
</p>\n";

// Test sur la présence de messages individuels:
$sql="SELECT * FROM messages WHERE login_destinataire!='' AND date_debut>='".(time()-10*30*24*3600)."' ORDER BY date_debut DESC;";
$test=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($test)==0) {
	echo "<p style='color:red'>Aucun message du Panneau d'affichage n'est adresse à un utilisateur en particulier.</p>";
	require("../lib/footer.inc.php");
	die();
}

echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<table class='boireaus boireaus_alt resizable sortable'>
			<thead>
				<tr>
					<th>Date début</th>
					<th>Date fin</th>
					<th>Message</th>
					<th>Destinataire</th>
					<th class='nosort'>
						Supprimer<br />
						<a href='#' onclick=\"tout_cocher_decocher(true); return false;\" title=\"Cocher tous les messages\"><img src='../images/enabled.png' class='icone20' /></a>/
						<a href='#' onclick=\"tout_cocher_decocher(false); return false;\" title=\"Décocher tous les messages\"><img src='../images/disabled.png' class='icone20' /></a>
					</th>
				</tr>
			</thead>
			<tbody>";
while($lig=mysqli_fetch_object($test)) {
	if(!isset($civ_nom_prenom[$lig->login_destinataire])) {
		$civ_nom_prenom[$lig->login_destinataire]=civ_nom_prenom($lig->login_destinataire);
	}
	echo "
				<tr>
					<td>
						<span style='display:none'>
							".strftime("%Y%m%d", $lig->date_debut)."
						</span>
						<label for='suppr_".$lig->id."' id='texte_suppr_".$lig->id."'>
							".strftime("%d/%m/%Y", $lig->date_debut)."
						</label>
					</td>
					<td>
						<span style='display:none'>
							".strftime("%Y%m%d", $lig->date_fin)."
						</span>
						".strftime("%d/%m/%Y", $lig->date_fin)."
					</td>
					<td>
						";
	//echo preg_replace('#<form .*</form>#', "", $lig->texte);
	$chaine=preg_replace('#<form #i', "<div style='display:none'", preg_replace('#</form>#i', "</div>", $lig->texte));
	echo preg_replace('#<input type="hidden" name="csrf_alea" value="_CSRF_ALEA_">#', '', $chaine);
	echo "
					</td>
					<td>".$civ_nom_prenom[$lig->login_destinataire]."</td>
					<td>
						<input type='checkbox' name='suppr[]' id='suppr_".$lig->id."' value=\"".$lig->id."\" onchange=\"checkbox_change(this.id); changement()\" />
					</td>
				</tr>";
}
echo "
			</tbody>
		</table>

		<input type='hidden' name='valider_les_suppressions' value='y' />
		<p><input type='submit' value='Supprimer les messages cochés' /></p>

		<div id='fixe'><input type='submit' value='Supprimer les messages cochés' /></div>

		<script type='text/javascript'>
			".js_checkbox_change_style()."

			function tout_cocher_decocher(mode) {
				champ=document.getElementsByTagName('input');
				//alert(champ.length);
				for(i=0;i<champ.length;i++) {
					type_champ=champ[i].getAttribute('type');
					if(type_champ=='checkbox') {
						champ[i].checked=mode;
						checkbox_change(champ[i].getAttribute('id'));
						changement();
					}
				}
			}
		</script>
	</fieldset>
</form>";

require("../lib/footer.inc.php");
?>
