<?php
/**
 *
 * Copyright 2015 Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_notanet/recherche_ine.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_notanet/recherche_ine.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Notanet : Recherche INE',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//**************** DEBUT EN-TETE ***************
$titre_page = "Recherche INE";
$_SESSION['cacher_header'] = "y";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "
<p style='margin-bottom:1em;'>
	<a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
</p>
<h1>Recherche INE</h1>
<p>Il arrive que lors de l'import Notanet, on ait des lignes d'erreur du type&nbsp;:<br />
<span style='color:red'>&nbsp;&nbsp;&nbsp;1234567890M Identifiant national inconnu dans la base de données</span><br />
Rechercher élève par élève de qui il s'agit pour s'apercevoir généralement que c'est un élève redoublant qui a eu son brevet l'année précédente, est fastidieux.<br />
La présente page permet de rechercher en <b>un</b> copier/coller de toutes les lignes de <span style='color:red'>1234567890M Identifiant national inconnu dans la base de données</span> de qui il s'agit.</p>

<br />

<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Coller ici les lignes affichées dans Notanet&nbsp;:<br />
		<textarea name='lignes_a_traiter' cols='80' rows='10'></textarea>
		</p>
		<p><input type='submit' value=\"Extraire le INE de ces lignes\" /></p>
	</fieldset>
</form>\n";

if(isset($_POST['lignes_a_traiter'])) {
	check_token(false);

	//echo "<pre>".$_POST['lignes_a_traiter']."</pre>";

	$retour=preg_match_all("/[0-9]{10}[A-Z]{1}/", $_POST['lignes_a_traiter'], $tab);
	/*
	echo "<pre>";
	print_r($tab);
	echo "</pre>";
	*/

	if(count($tab)==0) {
		echo "<br /><p style='color:red'>Aucun INE trouvé dans les lignes proposées.</p>";
		require_once("../lib/footer.inc.php");
		die();
	}

	echo "<br />
<p>".count($tab[0])." INE trouvé(s) dans les lignes proposées.</p>
<table class='boireaus boireaus_alt'>
	<thead>
		<tr>
			<th>INE</th>
			<th><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' title=\"Résumé élève présenté avec les onglets Élève, Responsables, Enseignements, Bulletins,...\" /></th>
			<th>Nom</th>
			<th>Prénom</th>
			<th>Naissance</th>
			<th>Redoublant</th>
			<th>Classe</th>
			<th>Extraire</th>
		</tr>
	</thead>
	<tbody>";
	for($loop=0;$loop<count($tab[0]);$loop++) {
		$sql="SELECT * FROM eleves WHERE no_gep='".$tab[0][$loop]."';";
		//echo "$sql<br />";
		$res = mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "
		<tr>
			<td>".$tab[$loop]."</td>
			<td colspan='3' style='color:red'>Non trouvé dans la base Gepi</td>
		</tr>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			$tab_ele=get_info_eleve($lig->login, 1);
			echo "
		<tr>
			<td>".$tab[0][$loop]."</td>
			<td><a href='../eleves/visu_eleve.php?ele_login=".$lig->login."' title=\"Voir le dossier élève dans un nouvel onglet\" target='_blank'><img src='../mod_trombinoscopes/images/photo_".(($lig->sexe=='F') ? "f" : "g").".png' class='icone16' alt='Onglets' /></a></td>
			<td><a href='../eleves/modify_eleve.php?eleve_login=".$lig->login."' title=\"Éditer la fiche élève dans un nouvel onglet\" target='_blank'>".$lig->nom."</a></td>
			<td>".$lig->prenom."</td>
			<td>".formate_date($lig->naissance)."</td>
			<td>".(((isset($tab_ele['doublant']))&&($tab_ele['doublant']=="R")) ? "Oui" : "")."</td>
			<td>".$tab_ele['classes']."</td>
			<td><a href='corrige_extract_moy.php?valider_select_eleve=y&ele_login[0]=".$lig->login."&extract_mode=select&afficher_select_eleve=y' target='_blank' title=\"Afficher l'extraction notanet pour cet élève... et éventuellement corriger.\">Extraire</a></td>
		</tr>";
		}
	}
	echo "
	</tbody>
</table>
<p><br /></p>";

}

require_once("../lib/footer.inc.php");
?>

