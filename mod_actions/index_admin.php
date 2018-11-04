<?php
/**
 * Actions (UNSS)
 * 
 * $_POST['activer'] activation/désactivation
 * $_POST['is_posted']
 * 
 *
 * @copyright Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 * @license GNU/GPL, 
 * @package Carnet_de_notes
 * @subpackage administration
 * @see checkAccess()
 * @see saveSetting()
 * @see suivi_ariane()
 */

/* This file is part of GEPI.
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
// Titre de page modifié plus bas
//$titre_page = "Actions";
$niveau_arbo = 1;
$gepiPathJava="./..";

/**
 * Fichiers d'initialisation
 */
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_actions/index_admin.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_actions/index_admin.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Actions : Administration',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/******************************************************************
 *    Enregistrement des variables passées en $_POST si besoin
 ******************************************************************/
$msg = '';
$post_reussi=FALSE;

//debug_var();

// Ménage:
$sql="DELETE FROM mod_actions_gestionnaires WHERE login_user NOT IN (SELECT login FROM utilisateurs);";
//echo "$sql<br />";
$menage=mysqli_query($mysqli, $sql);

if((isset($_POST['is_posted']))&&($_POST['is_posted']==1)) {
	check_token();

	if (isset($_POST['activer'])) {
		if (!saveSetting("active_mod_actions", $_POST['activer'])) $msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
	}

	if ((isset($_POST['terme_mod_action']))&&($_POST['terme_mod_action']!='')) {
		if (!saveSetting("terme_mod_action", $_POST['terme_mod_action'])) $msg = "Erreur lors de l'enregistrement du terme pour désigner les actions !";
	}
}

if((isset($_POST['is_posted']))&&($_POST['is_posted']==2)) {
	check_token();

	$msg='';

	$tab_categories_actions=get_tab_actions_categories();
	/*
	echo "<pre>";
	print_r($tab_categories_actions);
	echo "</pre>";
	*/

	$nom=isset($_POST['nom']) ? $_POST['nom'] : array();
	$description=isset($_POST['description']) ? $_POST['description'] : array();
	foreach($nom as $id_categorie => $categorie) {
		$sql="UPDATE mod_actions_categories SET nom='".$nom[$id_categorie]."', description='".$description[$id_categorie]."' WHERE id='".$id_categorie."';";
		//echo "$sql<br />";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg.="Erreur lors de la mise à jour de la catégorie n°$id_categorie&nbsp;:<br />$sql<br />";
		}
		else {
			$nb_suppr=0;
			$current_login_user=isset($_POST['login_user_'.$id_categorie]) ? $_POST['login_user_'.$id_categorie] : array();
			foreach($tab_categories_actions[$id_categorie]['gestionnaire'] as $cpt_user => $user) {
				if(!in_array($user, $current_login_user)) {
					$sql="DELETE FROM mod_actions_gestionnaires WHERE id_categorie='".$id_categorie."' AND login_user='".$user."';";
					//echo "$sql<br />";
					$delete=mysqli_query($mysqli, $sql);
					if(!$delete) {
						$msg.="Erreur lors de la suppression de $user pour la catégorie n°$id_categorie&nbsp;:<br />$sql<br />";
					}
					else {
						$nb_suppr++;
					}
				}
			}
			if($nb_suppr>0) {
				$msg.=$nb_suppr." gestionnaire(s) supprimé(s) pour la catégorie n°$id_categorie.<br />";
			}

			$nb_ajout=0;
			foreach($current_login_user as $cpt_user => $user) {
				if(($user!='')&&(!in_array($user, $tab_categories_actions[$id_categorie]['gestionnaire']))) {
					$sql="INSERT INTO mod_actions_gestionnaires SET id_categorie='".$id_categorie."', login_user='".$user."';";
					//echo "$sql<br />";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'ajout de $user pour la catégorie n°$id_categorie&nbsp;:<br />$sql<br />";
					}
					else {
						$nb_ajout++;
					}
				}
			}
			if($nb_ajout>0) {
				$msg.=$nb_ajout." gestionnaire(s) ajouté(s) pour la catégorie n°$id_categorie.<br />";
			}
		}
	}


	$nouvelle_categorie_nom=isset($_POST['nouvelle_categorie_nom']) ? $_POST['nouvelle_categorie_nom'] : NULL;
	$nouvelle_categorie_description=isset($_POST['nouvelle_categorie_nom']) ? $_POST['nouvelle_categorie_nom'] : NULL;
	$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : array();

	if((isset($nouvelle_categorie_nom))&&(isset($nouvelle_categorie_description))) {
		if(trim($nouvelle_categorie_nom)!='') {
		/*
		if(trim($nouvelle_categorie_nom)=='') {
			$msg="Le nom de catégorie ne peut pas être vide.<br />";
		}
		else {
		*/
			$sql="SELECT 1=1 FROM mod_actions_categories WHERE nom='".$nouvelle_categorie_nom."'";
			//echo "$sql<br />";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Il existe déjà une catégorie nommée <strong>".$nouvelle_categorie_nom."</strong>.<br />";
			}
			else {
				$sql="INSERT INTO mod_actions_categories SET nom='".$nouvelle_categorie_nom."', description='".$nouvelle_categorie_description."';";
				//echo "$sql<br />";
				$insert=mysqli_query($mysqli, $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'ajout de la catégorie <strong>".$nouvelle_categorie_nom."</strong>&nbsp;: ".$sql."<br />";
				}
				else {
					$id_categorie=mysqli_insert_id($mysqli);
					$msg.="Catégorie <strong>".$nouvelle_categorie_nom."</strong> ajoutée.<br />";

					$cpt_user=0;
					for($loop=0;$loop<count($login_user);$loop++) {

						$sql="INSERT INTO mod_actions_gestionnaires SET id_categorie='".$id_categorie."', login_user='".$login_user[$loop]."';";
						//echo "$sql<br />";
						$insert=mysqli_query($mysqli, $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'ajout du gestionnaire ".civ_nom_prenom($login_user[$loop])." la catégorie <strong>".$nouvelle_categorie_nom."</strong>&nbsp;: ".$sql."<br />";
						}
						else {
							$cpt_user++;
						}
					}
					if($cpt_user>0) {
						$msg.=$cpt_user." gestionnaire(s) ajoutés.<br />";
					}
				}
			}
		}
	}

}

if (isset($_POST['is_posted']) and ($msg=='')){
  $msg = "Les modifications ont été enregistrées !";
  $post_reussi=TRUE;
}

$terme_mod_action=getSettingValue('terme_mod_action');
if($terme_mod_action=='') {
	$terme_mod_action='Action';
}
$titre_page = $terme_mod_action."s";

// on demande une validation si on quitte sans enregistrer les changements
$messageEnregistrer="Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?";
/****************************************************************
                     HAUT DE PAGE
****************************************************************/

// ====== Inclusion des balises head et du bandeau =====
/**
 * Entête de la page
 */
include_once("../lib/header_template.inc.php");

/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/

if (!suivi_ariane($_SERVER['PHP_SELF'],$titre_page))
		echo "erreur lors de la création du fil d'ariane";

/****************************************************************
			BAS DE PAGE
****************************************************************/
$tbs_microtime="";
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


$nom_gabarit = '../templates/'.$_SESSION['rep_gabarits'].'/mod_actions/index_admin_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
/**
 * Inclusion du gabarit
 */
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuAffiche);

?>
