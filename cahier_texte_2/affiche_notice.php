<?php
/*
*
*  Copyright 2001, 2018 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/cahier_texte_2/affiche_notice.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/cahier_texte_2/affiche_notice.php',
administrateur='F',
professeur='V',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Cahier de texte 2 : Affichage notice',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

// Récupérer id_notice et type notice.
$id_ct=isset($_GET['id_ct']) ? $_GET['id_ct'] : NULL;
$type_notice=isset($_GET['type_notice']) ? $_GET['type_notice'] : NULL;

if((!isset($id_ct))||
(!isset($type_notice))||
(!preg_match('/^[0-9]{1,}$/', $id_ct))||
(($type_notice!='c')&&($type_notice!='t')&&($type_notice!='p'))) {
	header("Location: ../accueil.php?msg=Notice invalide");
	die();
}

// Pas d'entête

//**************** EN-TETE **************************************
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

// Afficher les infos groupe, date, type notice
// Lien notice précédente/suivante ?

// Afficher la notice

//debug_var();

$couleur_fond=$color_fond_notices[$type_notice];
echo "<div style='margin:0.5em; padding:0.5em; background-color:".$couleur_fond."; border: 1px solid black;'>";
// Mettre en float right un retour à la page d'accueil.
echo "
	<div style='float:right; width:16px; margin:0.5em;'>
		<a href='../accueil.php'><img src='../images/icons/home.png' class='icone16' alt='Accueil' /></a>
	</div>";

if($type_notice=='c') {
	$table_ct='ct_entry';
}
elseif($type_notice=='t') {
	$table_ct='ct_devoirs_entry';
}
else {
	$table_ct='ct_private_entry';
}
$sql="SELECT * FROM ".$table_ct." WHERE id_ct='".$id_ct."';";
$res=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>La notice n'existe pas.</p>";
	echo "</div>";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

$lig_ct=mysqli_fetch_object($res);

// Contrôler que la personne est propriétaire du CDT.
$sql="SELECT 1=1 FROM j_groupes_professeurs WHERE id_groupe='".$lig_ct->id_groupe."' AND login='".$_SESSION['login']."';";
$test=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p style='color:red'>Vous n'êtes pas propriétaire de ce CDT.</p>";
	echo "</div>";

	echo "<p><br /></p>\n";
	require("../lib/footer.inc.php");
	die();
}

// Et un autre vers visu CDT
echo "
	<div style='float:right; width:16px; margin:0.5em;'>
		<a href='../cahier_texte_2/see_all.php?id_groupe=".$lig_ct->id_groupe."'><img src='../images/icons/cahier_textes.png' class='icone16' alt='CDT' /></a>
	</div>

	<h2>".get_info_grp($lig_ct->id_groupe)."</h2>
	<h3>Séance du ".french_strftime("%A %d/%m/%Y", $lig_ct->date_ct);

// Lien séance précédente/suivante
// Problème lorsqu'on a deux heures dans la même journée (ajouter un test)
$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct<'".$id_ct."' AND date_ct='".$lig_ct->date_ct."' ORDER BY id_ct DESC;";
$res_mult=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_mult)>0) {
	$lig_prec=mysqli_fetch_object($res_mult);
	echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
}
else {
	$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct<'".$lig_ct->date_ct."' ORDER BY date_ct DESC limit 1;";
	$res_prec=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_prec)>0) {
		$lig_prec=mysqli_fetch_object($res_prec);
		echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_prec->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_prec->date_ct)."\"><img src='../images/icons/back.png' class='icone16' alt='Séance précédente' /></a>";
	}
}

$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct>'".$id_ct."' AND date_ct='".$lig_ct->date_ct."' ORDER BY id_ct ASC;";
$res_mult=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res_mult)>0) {
	$lig_suiv=mysqli_fetch_object($res_mult);
	echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
}
else {
	$sql="SELECT * FROM ".$table_ct." WHERE id_groupe='".$lig_ct->id_groupe."' AND id_ct!='".$id_ct."' AND date_ct>'".$lig_ct->date_ct."' ORDER BY date_ct ASC limit 1;";
	$res_suiv=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_suiv)>0) {
		$lig_suiv=mysqli_fetch_object($res_suiv);
		echo "
		 <a href='".$_SERVER['PHP_SELF']."?id_ct=".$lig_suiv->id_ct."&type_notice=".$type_notice."' title=\"Afficher la séance du ".french_strftime("%A %d/%m/%Y", $lig_suiv->date_ct)."\"><img src='../images/icons/forward.png' class='icone16' alt='Séance suivante' /></a>";
	}
}

	echo "
	</h3>";

echo "
	<div class='fieldset_opacite50' style='padding:0.5em'>";

$tab_tag_type=get_tab_tag_cdt();
$tab_tag_notice=get_tab_tag_notice($lig_ct->id_ct, $type_notice);
if(isset($tab_tag_notice["indice"])) {
	echo "
		<div style='float:right; width:16px; margin:0.5em;'>";
	for($loop_tag=0;$loop_tag<count($tab_tag_notice["indice"]);$loop_tag++) {
		echo "
			<img src='$gepiPath/".$tab_tag_notice["indice"][$loop_tag]['drapeau']."' class='icone16' alt=\"".$tab_tag_notice["indice"][$loop_tag]['nom_tag']."\" title=\"Un ".$tab_tag_notice["indice"][$loop_tag]['nom_tag']." est indiqué.\" /> ";
	}
	echo "
		</div>";
}

echo $lig_ct->contenu;

$adj=affiche_docs_joints($lig_ct->id_ct, $type_notice);
if($adj!='') {
	echo "
		<div style='border: 1px dashed black; margin-top:1em;'>
			".$adj.
		"</div>\n";
}

echo "
	</div>
</div>
<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
