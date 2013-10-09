<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

@set_time_limit(0);

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

//INSERT INTO droits SET id='/cahier_texte_2/ajax_cdt.php',administrateur='F',professeur='F',cpe='F',scolarite='F',eleve='V',responsable='V',secours='F',autre='F',description='Enregistrement des modifications sur CDT',statut='';
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

check_token();

header('Content-Type: text/html; charset=utf-8');

$mode=isset($_GET['mode']) ? $_GET['mode'] : "";
$login_eleve=isset($_GET['login_eleve']) ? $_GET['login_eleve'] : "";
$id_ct_devoir=isset($_GET['id_ct_devoir']) ? $_GET['id_ct_devoir'] : "";
//$etat=isset($_GET['etat']) ? $_GET['etat'] : "";

/*
echo "\$mode=$mode<br />";
echo "\$login_eleve=$login_eleve<br />";
echo "\$regime_eleve=$regime_eleve<br />";
*/

$CDTPeutPointerTravailFait=getSettingAOui('CDTPeutPointerTravailFait'.ucfirst($_SESSION['statut']));

//if(($mode=='changer_etat')&&($etat!="")&&(is_numeric($id_ct_devoir))&&($login_eleve!="")) {
if(($mode=='changer_etat')&&(is_numeric($id_ct_devoir))&&($login_eleve!="")&&($CDTPeutPointerTravailFait)) {
	if($_SESSION['statut']=='eleve') {
		if($login_eleve!=$_SESSION['login']) {
			echo "<img src='../images/icons/sens_interdit.png' class='icone16' title=\"Vous ne pouvez pas modifier les travux faits ou non pour un autre élève.
Notez que ces tentatives pourraient provoquer une désactivation de votre compte.\" />";
			tentative_intrusion(1, "Tentative d'un élève de modifier l'état du travail fait ou non CDT pour une notice d'un autre élève ($login_eleve).");
			die();
		}
	}
	elseif($_SESSION['statut']=='responsable') {
		if(!is_responsable($login_eleve, $_SESSION['login'], "", "yy")) {
			echo "<img src='../images/icons/sens_interdit.png' class='icone16' title=\"Vous ne pouvez pas modifier les travux faits ou non pour un élève dont vous n'êtes pas responsable.
Notez que ces tentatives pourraient provoquer une désactivation de votre compte.\" />";
			tentative_intrusion(1, "Tentative d'un responsable de modifier l'état du travail fait ou non CDT pour une notice d'un élève ($login_eleve) dont il n'est pas responsable.");
			die();
		}
	}

	// Vérifier que l'id_ct_devoir correspond à un cours de l'élève.
	$sql="SELECT 1=1 FROM ct_devoirs_entry cde, j_eleves_groupes jeg WHERE cde.id_ct='".$id_ct_devoir."' AND cde.id_groupe=jeg.id_groupe AND jeg.login='$login_eleve';";
	//echo "$sql<br />";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		echo "<img src='../images/icons/sens_interdit.png' class='icone16' title=\"Vous ne suivez pas l'enseignement associé à ce devoir.\" />";
		die();
	}

	$date_courante=strftime("%Y-%m-%d %H:%M:%S");
	$sql="SELECT * FROM ct_devoirs_faits WHERE id_ct='".$id_ct_devoir."' AND login='$login_eleve';";
	$res=mysql_query($sql);
	if(mysql_num_rows($res)==0) {
		$nouvel_etat="fait";
		$sql="INSERT INTO ct_devoirs_faits SET id_ct='".$id_ct_devoir."', login='$login_eleve', date_initiale='$date_courante', date_modif='$date_courante', etat='$nouvel_etat', commentaire='';";
	}
	else {
		$etat=mysql_result($res, 0, 'etat');
		if($etat=="") {
			$nouvel_etat='fait';
		}
		else {
			$nouvel_etat='';
		}
		$sql="UPDATE ct_devoirs_faits SET date_modif='$date_courante', etat='$nouvel_etat', commentaire='' WHERE id_ct='".$id_ct_devoir."' AND login='$login_eleve';";
	}
	$reg=mysql_query($sql);
	if(!$reg) {
		//echo "<img src='../images/icons/ico_attention.png' class='icone16' title=\"Il s'est produit une erreur.\" />";
		echo "<img src='../images/icons/ico_attention.png' class='icone16' title=\"Il s'est produit une erreur.
$sql\" />";
	}
	elseif($nouvel_etat=='fait') {
		echo "<a href=\"javascript:cdt_modif_etat_travail('$login_eleve', '".$id_ct_devoir."')\" title=\"FAIT: Le travail est fait\"><img src='../images/edit16b.png' class='icone16' /></a>";
	}
	else {
		echo "<a href=\"javascript:cdt_modif_etat_travail('$login_eleve', '".$id_ct_devoir."')\" title=\"NON FAIT: Le travail n'est pas encore fait\"><img src='../images/edit16.png' class='icone16' /></a>";
	}

	// NOTE: En cas de modification du devoir par le prof, il faut faire un UPDATE sur les ct_devoirs_faits et mettre un commentaire comme quoi le prof a fait une modification sur la notice.
}

?>
