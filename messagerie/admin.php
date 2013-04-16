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
$titre_page = "Messagerie : Activation/désactivation";
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


$sql="SELECT 1=1 FROM droits WHERE id='/messagerie/admin.php';";
$test=mysql_query($sql);
if(mysql_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/messagerie/admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Messagerie : Administration du module',
statut='';";
$insert=mysql_query($sql);
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

	if ((isset($_POST['activer']))&&(!saveSetting("active_messagerie", $_POST['activer']))) {
		$msg .= "Erreur lors de l'enregistrement du paramètre activation/désactivation !<br />";
	} else {
		$nb_reg++;
	}

	if (isset($_POST['MessagerieAvecSon'])) {
		if(!saveSetting("MessagerieAvecSon", $_POST['MessagerieAvecSon'])) {
			$msg .= "Erreur lors de l'enregistrement du paramètre Messagerie avec son !<br />";
		} else {
			$nb_reg++;
		}
	}

	saveSetting("PeutPosterMessageAdministrateur", 'y');

	$tab_statut=array('Professeur', 'Scolarite', 'Cpe');
	for($loop=0;$loop<count($tab_statut);$loop++) {
		if (isset($_POST['PeutPosterMessage'.$tab_statut[$loop]])) {
			$valeur="y";
		}
		else {
			$valeur="n";
		}

		if(!saveSetting('PeutPosterMessage'.$tab_statut[$loop], $valeur)) {
			$msg .= "Erreur lors de l'enregistrement du paramètre PeutPosterMessage".$tab_statut[$loop]." !<br />";
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
		$msg="Aucune date limite n'a été saisie.<br />\n";
	}
	else {
		$sql="DELETE FROM messagerie WHERE date_msg < '$annee-$mois-$jour 00:00:00';";
		//echo "$sql<br />\n";
		$suppr=mysql_query($sql);
		if($suppr) {
			$msg="Les messages antérieurs au $jour/$mois/$annee ont été supprimés.<br />\n";
			$post_reussi=TRUE;
		}
		else {
			$msg="Erreur lors de la suppression des messages antérieurs au $jour/$mois/$annee.<br />\n";
		}
	}
}


// ====== Inclusion des balises head et du bandeau =====
include_once("../lib/header_template.inc.php");

if (!suivi_ariane($_SERVER['PHP_SELF'],"Gestion Messagerie"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

include("../lib/calendrier/calendrier.class.php");
$cal = new Calendrier("form2", "date_limite");

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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/messagerie/admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

?>
