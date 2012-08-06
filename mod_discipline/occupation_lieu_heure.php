<?php

/*
 *
 * Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

if(mb_strtolower(mb_substr(getSettingValue('active_mod_discipline'),0,1))!='y') {
	$mess=rawurlencode("Vous tentez d accéder au module Discipline qui est désactivé !");
	tentative_intrusion(1, "Tentative d'accès au module Discipline qui est désactivé.");
	header("Location: ../accueil.php?msg=$mess");
	die();
}

require('sanctions_func_lib.php');

$msg="";

$id_sanction=isset($_POST['id_sanction']) ? $_POST['id_sanction'] : (isset($_GET['id_sanction']) ? $_GET['id_sanction'] : NULL);

$lieu=isset($_GET['lieu']) ? $_GET['lieu'] : NULL;
$date=isset($_GET['date']) ? $_GET['date'] : NULL;
$heure=isset($_GET['heure']) ? $_GET['heure'] : NULL;
$duree=isset($_GET['duree']) ? $_GET['duree'] : NULL;

$tmp_date=explode("/",$date);
if(!checkdate($tmp_date[1],$tmp_date[0],$tmp_date[2])) {
	$msg.="La date saisie n'est pas valide.<br />";
}

$l_duree=mb_strlen($duree);
$duree=preg_replace("/,/",".",preg_replace("/[^0-9.]/","",$duree));
if($duree=="") {
	$duree=1;
	$msg.="La durée de retenue saisie n'était pas correcte. Elle a été remplacée par '1'.<r />";
}
elseif($l_duree!=mb_strlen($duree)) {
	$msg.="La durée de retenue saisie n'était pas correcte. Elle a été modifiée.<r />";
}

$utilisation_prototype="ok";
$mode_header_reduit="y";
//**************** EN-TETE *****************
$titre_page = "Discipline: Occupation d'un lieu";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour index</a>\n";
echo "</p>\n";

/*
// On arrive à récupérer les infos sur la page source via JavaScript, mais on ne peut pas ensuite aisément faire des requêtes PHP/MySQL
echo "<script type='text/javascript'>
	alert(window.opener.document.getElementById('lieu_retenue').value);
</script>\n";
*/

//if((!isset($lieu))||(!isset($date))||(!isset($heure))) {
//if(!isset($id_sanction)) {
if((!isset($lieu))||(!isset($date))||(!isset($heure))||(!isset($duree))||(!isset($id_sanction))) {
	echo "<p><strong>Erreur&nbsp;:</strong> Des paramètres n'ont pas été transmis.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

$sql="SELECT * FROM edt_creneaux ORDER BY heuredebut_definie_periode;";
$res_abs_cren=mysql_query($sql);
if(mysql_num_rows($res_abs_cren)==0) {
	echo "<p>La table edt_creneaux n'est pas renseignée!</p>\n";
	require("../lib/footer.inc.php");
	die();
}
else {
	$tab_creneaux=array();
	while($lig_ac=mysql_fetch_object($res_abs_cren)) {
		$cpt=$lig_ac->id_definie_periode;

		$tab_creneaux["$lig_ac->nom_definie_periode"]=array();

		$tab_creneaux["$lig_ac->nom_definie_periode"]['debut']=$lig_ac->heuredebut_definie_periode;
		$tab_tmp=explode(":",$lig_ac->heuredebut_definie_periode);
		$tab_creneaux["$lig_ac->nom_definie_periode"]['debut_sec']=$tab_tmp[0]*3600+$tab_tmp[1]*60+$tab_tmp[2];

		$tab_creneaux["$lig_ac->nom_definie_periode"]['fin']=$lig_ac->heurefin_definie_periode;
		$tab_tmp=explode(":",$lig_ac->heurefin_definie_periode);
		$tab_creneaux["$lig_ac->nom_definie_periode"]['fin_sec']=$tab_tmp[0]*3600+$tab_tmp[1]*60+$tab_tmp[2];

		if($heure==$lig_ac->nom_definie_periode) {
			$heure_debut_sec=$tab_creneaux["$lig_ac->nom_definie_periode"]['debut_sec'];
			$heure_fin_sec=$heure_debut_sec+3600*$duree;
		}
	}
}

echo "<p class='bold'>Liste des retenues (<em>et assimilées</em>) en ";
if($lieu!="") {
	echo $lieu;
}
else {
	echo "<span style='color:red;'>lieu indéfini</span>";
}
echo " le ".$date." pouvant se chevaucher avec le créneau ".$heure." (<em>".$tab_creneaux["$heure"]['debut']." à ";
if($duree==1) {
	echo $tab_creneaux["$heure"]['fin'];
}
else {
	echo secondes_to_hms($tab_creneaux["$heure"]['debut_sec']+3600*$duree);
}
echo "</em>)</p>\n";

echo "<blockquote>\n";

$date_mysql=formate_date_mysql($date);

$sql="SELECT * FROM s_retenues sr, s_sanctions s WHERE sr.id_sanction!='$id_sanction' AND sr.date='$date_mysql' AND sr.lieu='$lieu' AND s.id_sanction=sr.id_sanction ORDER BY sr.heure_debut, s.login;";
//echo "$sql<br />";
$res=mysql_query($sql);
if(mysql_num_rows($res)==0) {
	echo "<p>Aucune autre retenue de la journée dans ce lieu.</p>\n";
	echo "</blockquote>\n";
	require("../lib/footer.inc.php");
	die();
}

$chaine_retenues="";
$alt=1;
// Mettre dans un tableau les retenues pour calculer heure+durée s'il y a des intersections.
while($lig=mysql_fetch_object($res)) {
	if(
		(($heure_debut_sec>=$tab_creneaux["$lig->heure_debut"]['debut_sec'])&&($heure_debut_sec<=$tab_creneaux["$lig->heure_debut"]['fin_sec']))||
		(($heure_fin_sec>=$tab_creneaux["$lig->heure_debut"]['debut_sec'])&&($heure_fin_sec<=$tab_creneaux["$lig->heure_debut"]['fin_sec']))||
		(($heure_debut_sec<=$tab_creneaux["$lig->heure_debut"]['debut_sec'])&&($heure_fin_sec>=$tab_creneaux["$lig->heure_debut"]['fin_sec']))) {

		// Il y a intersection... on affiche la retenue...

		$alt=$alt*(-1);
		$chaine_retenues.="<tr class='lig$alt'>\n";
		$chaine_retenues.="<td>".$lig->nature."</td>\n";
		$chaine_retenues.="<td>".$lig->heure_debut;
		if($lig->duree>1) {$chaine_retenues.=" +";}
		$chaine_retenues.="</td>\n";
		$chaine_retenues.="<td>".$tab_creneaux["$lig->heure_debut"]['debut']."</td>\n";

		if($lig->duree==1) {
			$chaine_retenues.="<td>".$tab_creneaux["$lig->heure_debut"]['fin']."</td>\n";
		}
		else {
			$fin_retenue_courante=$tab_creneaux["$lig->heure_debut"]['debut_sec']+3600*$lig->duree;
			$fin_retenue_courante=secondes_to_hms($fin_retenue_courante);
			$chaine_retenues.="<td>~".$fin_retenue_courante."</td>\n";
		}

		$chaine_retenues.="<td>".$lig->duree."</td>\n";
		$chaine_retenues.="<td>".p_nom($lig->login);
		//$chaine_retenues.="</td>\n"
		//$chaine_retenues.="<td>\n";
		$chaine_retenues.=infobulle_photo($lig->login)."</td>\n";
		$chaine_retenues.="</tr>\n";

	}
}

if($chaine_retenues=="") {
	echo "<p>Aucune autre retenue de la journée ne se chevauche avec la retenue choisie dans ce lieu.</p>\n";
}
else {
	echo "<table class='boireaus' border='1' summary='Autres retenues sur le créneau'>\n";
	echo "<tr>\n";
	echo "<th>Nature</th>\n";
	echo "<th colspan='3'>Heure</th>\n";
	echo "<th>Durée<br />(<em>en heures</em>)</th>\n";
	echo "<th colspan='2'>Elève</th>\n";
	echo "</tr>\n";
	echo $chaine_retenues;
	echo "</table>\n";
}
echo "</blockquote>\n";

echo "<p><br /></p>\n";

echo "<p><em>Remarque&nbsp;:</em></p>\n";
echo "<blockquote>\n";
echo "<p>Cette page est destinée à déterminer si certains regroupements d'élèves sont à éviter pour des retenues (<em>et sanctions du même type</em>).</p>\n";
echo "</blockquote>\n";
echo "<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
