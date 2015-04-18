<?php
/*
* Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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
$accessibilite="y";
$titre_page = "Alertes : Activation/désactivation";
$niveau_arbo = 1;
$gepiPathJava="./..";
$post_reussi=FALSE;
$msg = '';


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


$sql="SELECT 1=1 FROM droits WHERE id='/mod_alerte/admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_alerte/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Dispositif d alerte : Administration du module',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

//======================================================================================
// Section checkAccess() à décommenter en prenant soin d'ajouter le droit correspondant:
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//$msg = '';
if (isset($_POST['is_posted'])) {
	check_token();

	$nb_reg=0;
	$msg="";

	if ((isset($_POST['activer']))&&(!saveSetting("active_mod_alerte", $_POST['activer']))) {
		$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre activation/désactivation !</span><br />";
	} else {
		$nb_reg++;
	}

	if (isset($_POST['MessagerieAvecSon'])) {
		if(!saveSetting("MessagerieAvecSon", $_POST['MessagerieAvecSon'])) {
			$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre Alertes avec son !</span><br />";
		} else {
			$nb_reg++;
		}
	}

	if (isset($_POST['MessagerieDelaisTest'])) {
		$MessagerieDelaisTest=$_POST['MessagerieDelaisTest'];
		if(($MessagerieDelaisTest=='')||(!preg_match('/^[0-9]*$/', $MessagerieDelaisTest))||($MessagerieDelaisTest<1)) {
			$msg .= "<span style='color:red'>La valeur proposée pour le paramètre Délais de test de présence d'une alerte est invalide !</span><br />";
		}
		else {
			if(!saveSetting("MessagerieDelaisTest", $MessagerieDelaisTest)) {
				$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre Délais de test de présence d'alerte !</span><br />";
			} else {
				$nb_reg++;
			}
		}
	}

	if (isset($_POST['MessagerieLargeurImg'])) {
		$MessagerieLargeurImg=$_POST['MessagerieLargeurImg'];
		if(($MessagerieLargeurImg=='')||(!preg_match('/^[0-9]*$/', $MessagerieLargeurImg))||($MessagerieLargeurImg<1)) {
			$msg .= "<span style='color:red'>La valeur proposée pour le paramètre Largeur de l'image signalant des alertes non lues est invalide !</span><br />";
		}
		else {
			if(!saveSetting("MessagerieLargeurImg", $MessagerieLargeurImg)) {
				$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre Largeur de l'image signalant des alertes non lues !</span><br />";
			} else {
				$nb_reg++;
			}
		}
	}

	saveSetting("PeutPosterMessageAdministrateur", 'y');

	$tab_statut=array('Professeur', 'Scolarite', 'Cpe', 'Autre');
	for($loop=0;$loop<count($tab_statut);$loop++) {
		if (isset($_POST['PeutPosterMessage'.$tab_statut[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!saveSetting('PeutPosterMessage'.$tab_statut[$loop], $valeur)) {
			$msg .= "<span style='color:red'>Erreur lors de l'enregistrement du paramètre PeutPosterMessage".$tab_statut[$loop]." !</span><br />";
		} else {
			$nb_reg++;
		}
	}

	if($nb_reg>0) {
		$msg .= "Enregistrement effectué ($nb_reg valeurs).<br />";
		$post_reussi=TRUE;
	}
}

$date_limite=isset($_POST['date_limite']) ? $_POST['date_limite'] : strftime("%d/%m/%Y");
if (isset($_POST['is_posted2'])) {
	check_token();
	if(isset($date_limite)) {
		$tmp_tab=explode("/",$date_limite);
		$jour=$tmp_tab[0];
		$mois=$tmp_tab[1];
		$annee=$tmp_tab[2];

		if(!checkdate($mois,$jour,$annee)) {
			$msg="La date saisie $date_limite n'est pas valide.<br />\n";
			unset($date_limite);
		}
	}

	if(!isset($date_limite)) {
		$msg="<span style='color:red'>Aucune date limite n'a été saisie.</span><br />\n";
	}
	else {
		$sql="DELETE FROM messagerie WHERE date_msg < '$annee-$mois-$jour 00:00:00';";
		//echo "$sql<br />\n";
		$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
		if($suppr) {
			$msg="Les alertes antérieures au $jour/$mois/$annee ont été supprimées.<br />\n";
			$post_reussi=TRUE;
		}
		else {
			$msg="<span style='color:red'>Erreur lors de la suppression des alertes antérieures au $jour/$mois/$annee.</span><br />\n";
		}
	}
}

if (isset($_POST['is_posted3'])) {
	check_token();

	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();

	$tab_user_mae=array();

	$sql="SELECT value FROM mod_alerte_divers WHERE name='login_exclus';";
	$res_mae=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_mae)>0) {
		while($lig_mae=mysqli_fetch_object($res_mae)) {
			$tab_user_mae[]=$lig_mae->value;
		}
	}

	$cpt_comptes_exclus_ajoutes=0;
	for($loop=0;$loop<count($login_user);$loop++) {
		if(!in_array($login_user[$loop], $tab_user_mae)) {
			$sql="INSERT INTO mod_alerte_divers SET name='login_exclus', value='".$login_user[$loop]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			if($insert) {
				$cpt_comptes_exclus_ajoutes++;
			}
		}
	}
	$msg="$cpt_comptes_exclus_ajoutes compte(s) exclu(s) du module Alertes pris en compte.<br />";

	$cpt_comptes_exclus_supprimes=0;
	for($loop=0;$loop<count($tab_user_mae);$loop++) {
		if(!in_array($tab_user_mae[$loop], $login_user)) {
			$sql="DELETE FROM mod_alerte_divers WHERE name='login_exclus' AND value='".$login_user[$loop]."';";
			$delete=mysqli_query($GLOBALS["mysqli"], $sql);
			if($delete) {
				$cpt_comptes_exclus_supprimes++;
			}
		}
	}

	$msg.="$cpt_comptes_exclus_supprimes compte(s) précédemment exclu(s) du module Alertes ne le sont plus.<br />";
}

if(isset($_POST['is_posted_matieres_exclues'])) {
	check_token();

	$msg="";
	$mat_exclue=isset($_POST['mat_exclue']) ? $_POST['mat_exclue'] : array();

	$sql="DELETE FROM mod_alerte_divers WHERE name='matieres_exclues';";
	$del=mysqli_query($GLOBALS["mysqli"], $sql);

	$nb_mat_exclue=0;
	for($loop=0;$loop<count($mat_exclue);$loop++) {
		$sql="INSERT INTO mod_alerte_divers SET name='matieres_exclues', value='".$mat_exclue[$loop]."';";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$insert) {
			$msg.="Erreur lors de l'enregistrement de l'exclusion de ".$mat_exclue[$loop]."<br />";
		}
		else {
			$nb_mat_exclue++;
		}
	}

	$msg.="$nb_mat_exclue matières seront exclues des Équipes pédagogiques pour les alertes.<br />";
}

$tab_mat_exclue=array();
$sql="SELECT value FROM mod_alerte_divers WHERE name='matieres_exclues' ORDER BY value;";
$res_mat_exclue=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_mat_exclue)>0) {
	while($lig_mat_exclue=mysqli_fetch_object($res_mat_exclue)) {
		$tab_mat_exclue[]=$lig_mat_exclue->value;
	}
}

$tab_mat=array();
$sql="SELECT * FROM matieres ORDER BY matiere;";
$res_mat=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_mat)>0) {
	$cpt_mat=0;
	while($lig_mat=mysqli_fetch_object($res_mat)) {
		$tab_mat[$cpt_mat]['matiere']=$lig_mat->matiere;
		$tab_mat[$cpt_mat]['nom_complet']=$lig_mat->nom_complet;
		$cpt_mat++;
	}
}

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion Alertes"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

//include("../lib/calendrier/calendrier.class.php");
//$cal = new Calendrier("form2", "date_limite");

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime	="";
$tbs_pmv="";
require_once ("../lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var();


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_alerte/admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

?>
