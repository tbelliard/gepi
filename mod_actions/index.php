<?php
/*
 * Copyright 2001, 2018 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Régis Bouguin, Stephane Boireau
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

if(($_SESSION['statut']=='eleve')||($_SESSION['statut']=='responsable')) {
	header("Location: ./accueil.php");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_actions/index.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_actions/index.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='V',
description='Actions : Index',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//debug_var();

if(!isset($msg)) {
	$msg='';
}

//==============================================================================
// Récupération des variables transmises et vérifications diverses sur ces variables

$terme_mod_action=getSettingValue('terme_mod_action');
$terme_mod_action_nettoye=str_replace("'", " ", str_replace('"', " ", $terme_mod_action));

$id_categorie=isset($_POST['id_categorie']) ? $_POST['id_categorie'] : (isset($_GET['id_categorie']) ? $_GET['id_categorie'] : NULL);
if(!isset($id_categorie)) {
	if(!acces_mod_action('')) {
		header("Location: ../accueil.php?msg=Vous n avez pas accès au module ".$terme_mod_action.".");
		die();
	}
}
else {
	if(!acces_mod_action($id_categorie)) {
		$msg.="Vous n'avez pas accès à la catégorie n°".$id_categorie."<br />";
		unset($id_categorie);
	}
}

$id_action=isset($_POST['id_action']) ? $_POST['id_action'] : (isset($_GET['id_action']) ? $_GET['id_action'] : NULL);
if(isset($id_action)) {
	if(!preg_match('/^[0-9]{1,}$/', $id_action)) {
		$msg.="Identifiant d'action invalide&nbsp;: $id_action<br />";
		unset($id_action);
	}
	else {

		// Tester la catégorie associée
		// Vérifier si l'utilisateur a accès
		$sql="SELECT id_categorie FROM mod_actions_action WHERE id='".$id_action."';";
		//echo "$sql<br />";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)==0) {
			$msg.="L'action choisie n°".$id_action." n'existe pas.<br />";
			unset($id_action);
		}
		else {
			$lig=mysqli_fetch_object($test);
			$id_categorie=$lig->id_categorie;
			if(!acces_mod_action($id_categorie)) {
				$msg.="Vous n'avez pas accès à la catégorie n°".$id_categorie."<br />";
				unset($id_categorie);
			}
		}
	}
}

// A ce stade, si un id_action est choisi/correct, c'est forcément un nombre entier et id_categorie est alors aussi correct/entier et défini.

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
if((isset($id_classe))&&(!preg_match('/^[0-9]{1,}$/', $id_classe))) {
	$msg.="Identifiant de classe invalide&nbsp;: $id_classe<br />";
	unset($id_classe);
}

$id_action_inscriptions=isset($_POST['id_action_inscriptions']) ? $_POST['id_action_inscriptions'] : (isset($_GET['id_action_inscriptions']) ? $_GET['id_action_inscriptions'] : NULL);
if((isset($id_action_inscriptions))&&(!preg_match('/^[0-9]{1,}$/', $id_action_inscriptions))) {
	$msg.="Identifiant d'action/inscriptions invalide&nbsp;: $id_action_inscriptions<br />";
	unset($id_action_inscriptions);
}

// A ce stade, si un id_classe est choisi/correct, c'est forcément un nombre entier.

function get_mod_actions_liste_inscriptions_par_defaut($id_categorie) {
	global $mysqli;

	$tab=array();
	if(preg_match('/^[0-9]{1,}$/', $id_categorie)) {
		$sql="SELECT * FROM setting WHERE NAME='mod_actions_inscriptions_defaut_".$id_categorie."';";
		//echo "$sql<br />";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)>0) {
			$lig=mysqli_fetch_object($res);
			$id_action=$lig->VALUE;

			$sql="SELECT * FROM mod_actions_inscriptions WHERE id_action='".$id_action."';";
			$res2=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					$tab[]=$lig2->login_ele;
				}
			}
		}
	}
	return $tab;
}

// Ménage :
$sql="SELECT * FROM setting WHERE NAME LIKE 'mod_actions_inscriptions_defaut_%';";
//echo "$sql<br />";
$res=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_object($res)) {
		$tmp_id_action=$lig->VALUE;

		$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$tmp_id_action."';";
		$res2=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res2)==0) {
			$sql="DELETE FROM setting WHERE NAME='".$lig->NAME."';";
			//echo "$sql<br />";
			$del=mysqli_query($mysqli, $sql);
		}
	}
}


/*
echo "id_action=$id_action<br />
mode=$mode<br />";
*/

//==============================================================================
// Supprimer une action
if((isset($id_action))&&(isset($mode))&&($mode=='suppr')) {
	check_token();
	//$msg='';

	$sql="DELETE FROM mod_actions_inscriptions WHERE id_action='".$id_action."';";
	//echo "$sql<br />";
	$del=mysqli_query($mysqli, $sql);
	if(!$del) {
		$msg.="Erreur lors de la suppression des inscriptions associées à l'action n°".$id_action."<br />";
	}
	else {
		$sql="DELETE FROM mod_actions_action WHERE id='".$id_action."';";
		//echo "$sql<br />";
		$del=mysqli_query($mysqli, $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression de l'action n°".$id_action."<br />";
		}
		else {
			$msg.="Action n°".$id_action." supprimée (".strftime('%d/%m/%Y à %H:%M:%S').").<br />";
		}
	}
	unset($id_action);
	unset($mode);
}

//==============================================================================
// Valider l'ajout d'une action
if(isset($_POST['valider_ajout_action'])) {
	check_token();
	//$msg='';

	// Vérifier les chaines transmises
	$enregistrer=true;

	$nom_action=isset($_POST['nom_action']) ? $_POST['nom_action'] : '';
	if(!preg_match('/^[A-Za-z0-9]{1,}/', $nom_action)) {
		$msg.="Le nom de l'action '$nom_action' n'est pas valide.<br />Il doit débuter par une lettre ou un chiffre.<br />";
		$enregistrer=false;
	}

	$description=isset($_POST['description']) ? $_POST['description'] : '';
	$date_action=isset($_POST['date_action']) ? $_POST['date_action'] : NULL;
	if((!isset($date_action))||(!check_date($date_action))) {
		$msg.="La date de l'action '$date_action' n'est pas valide.<br />";
		$enregistrer=false;
	}
	else {
		$date_action=get_mysql_date_from_slash_date($date_action, 'n');
	}

	$heure_action=isset($_POST['heure_action']) ? $_POST['heure_action'] : NULL;
	if((!isset($heure_action))||(!check_heure($heure_action))) {
		$msg.="L'heure de début de l'action '$heure_action' n'est pas valide.<br />";
		$enregistrer=false;
	}

	if($enregistrer) {
		$sql="INSERT INTO mod_actions_action SET id_categorie='".$id_categorie."', nom='".$nom_action."', description='".$description."', date_action='".$date_action.' '.$heure_action.":00';";
		//echo "$sql<br />";
		$insert=mysqli_query($mysqli, $sql);
		if(!$insert) {
			$msg.="Erreur lors de l'enregistrement de l'action&nbsp;:<br />".$sql."<br />";
			$mode='ajout_action';
		}
		else {
			$id_action=mysqli_insert_id($mysqli);

			$complement_msg='';
			if((isset($_POST['inscrire_eleves_liste_defaut']))&&($_POST['inscrire_eleves_liste_defaut']=='y')) {
				$tab=get_mod_actions_liste_inscriptions_par_defaut($id_categorie);
				$cpt_reg_ele=0;
				foreach($tab as $cpt_ele => $login_ele) {
					$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$login_ele."';";
					$insert=mysqli_query($mysqli, $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'inscription de ".get_nom_prenom_eleve($login_ele)."&nbsp;:<br />".$sql."<br />";
					}
					else {
						$cpt_reg_ele++;
					}
				}

				if($cpt_reg_ele>0) {
					$complement_msg=' ('.$cpt_reg_ele.' élève(s) inscrit(s))';
				}
			}

			$msg.="Action enregistrée".$complement_msg." (".strftime('%d/%m/%Y à %H:%M:%S').").<br />";
			$mode='afficher';
		}
	}
	else {
		$mode='ajout_action';
	}
}

//==============================================================================
// Valider la modification d'une action
if((isset($_POST['valider_modif_action']))&&(isset($id_action))) {
	check_token();
	//$msg='';

	// Vérifier les chaines transmises
	$enregistrer=true;

	$nom_action=isset($_POST['nom_action']) ? $_POST['nom_action'] : '';
	if(!preg_match('/^[A-Za-z0-9]{1,}/', $nom_action)) {
		$msg.="Le nom de l'action '$nom_action' n'est pas valide.<br />Il doit débuter par une lettre ou un chiffre.<br />";
		$enregistrer=false;
	}

	$description=isset($_POST['description']) ? $_POST['description'] : '';
	$date_action=isset($_POST['date_action']) ? $_POST['date_action'] : NULL;
	if((!isset($date_action))||(!check_date($date_action))) {
		$msg.="La date de l'action '$date_action' n'est pas valide.<br />";
		$enregistrer=false;
	}
	else {
		$date_action=get_mysql_date_from_slash_date($date_action, 'n');
	}

	$heure_action=isset($_POST['heure_action']) ? $_POST['heure_action'] : NULL;
	if((!isset($heure_action))||(!check_heure($heure_action))) {
		$msg.="L'heure de début de l'action '$heure_action' n'est pas valide.<br />";
		$enregistrer=false;
	}

	if($enregistrer) {
		$sql="UPDATE mod_actions_action SET id_categorie='".$id_categorie."',nom='".$nom_action."', description='".$description."', date_action='".$date_action.' '.$heure_action.":00' WHERE id='".$id_action."';";
		//echo "$sql<br />";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg.="Erreur lors de la mise à jour de l'action&nbsp;:<br />".$sql."<br />";
			$mode='afficher';
		}
		else {
			$msg.="Action mise à jour (".strftime('%d/%m/%Y à %H:%M:%S').").<br />";
			$mode='afficher';
		}
	}
	else {
		$mode='afficher';
	}
}
//echo "id_categorie=$id_categorie<br />";
//==============================================================================
// Inscription d'un élève
if((isset($id_action))&&(isset($mode))&&($mode=='inscriptions')&&(isset($_GET['login_ele']))&&(trim($_GET['login_ele'])!='')) {
	check_token();
	/*
	$_GET['id_categorie']=	1
	$_GET['id_action']=	8
	$_GET['mode']=	inscriptions
	$_GET['id_classe']=	38
	$_GET['login_ele']=	dugenou
	$_GET['csrf_alea']=	XXXXXXXXXXX
	*/

	$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$_GET['login_ele']."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)>0) {
		$msg.=get_nom_prenom_eleve($_GET['login_ele'])." est déjà inscrit(e).<br />";
	}
	else {
		$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$_GET['login_ele']."';";
		$insert=mysqli_query($mysqli, $sql);
		if(!$insert) {
			$msg.="Erreur lors de l'inscription de ".get_nom_prenom_eleve($_GET['login_ele'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['login_ele'])." est inscrit(e).<br />";
		}
	}
}

//==============================================================================
// Inscription d'une liste d'élèves
if((isset($id_action))&&(isset($mode))&&($mode=='inscriptions')&&(isset($_POST['tab_login_ele']))&&(is_array($_POST['tab_login_ele']))) {
	check_token();
	
	$tab_login_ele=$_POST['tab_login_ele'];

	$cpt_reg=0;
	foreach($tab_login_ele as $key => $value) {
		$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$value."';";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			$msg.=get_nom_prenom_eleve($value)." est déjà inscrit(e).<br />";
		}
		else {
			$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$value."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'inscription de ".get_nom_prenom_eleve($value)."&nbsp;:<br />".$sql."<br />";
			}
			else {
				//$msg.=get_nom_prenom_eleve($value)." est inscrit(e).<br />";
				$cpt_reg++;
			}
		}
	}
	if($cpt_reg>0) {
		$msg.=$cpt_reg." élève(s) inscrit(s).<br />";
	}
}

//==============================================================================
// Désinscription d'un élève
if((isset($id_action))&&(isset($mode))&&(($mode=='inscriptions')||($mode=='presence'))&&(isset($_GET['suppr_ele']))&&(trim($_GET['suppr_ele'])!='')) {
	check_token();
	/*
	$_GET['id_categorie']=	1
	$_GET['id_action']=	8
	$_GET['mode']=	inscriptions
	$_GET['id_classe']=	38
	$_GET['suppr_ele']=	dugenou
	$_GET['csrf_alea']=	XXXXXXXXXXX
	*/

	$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$_GET['suppr_ele']."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		$msg.=get_nom_prenom_eleve($_GET['suppr_ele'])." n'est pas inscrit(e).<br />";
	}
	else {
		$sql="DELETE FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$_GET['suppr_ele']."';";
		$insert=mysqli_query($mysqli, $sql);
		if(!$insert) {
			$msg.="Erreur lors de la suppression de l'inscription de ".get_nom_prenom_eleve($_GET['suppr_ele'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['suppr_ele'])." est désinscrit(e).<br />";
		}
	}
}
//==============================================================================
// Désinscription de tous
if((isset($id_action))&&(isset($mode))&&(($mode=='inscriptions')||($mode=='presence'))&&(isset($_GET['vider_inscriptions']))&&(trim($_GET['vider_inscriptions'])=='y')) {
	check_token();

	$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		$msg.="Aucun élève n'était inscrit.<br />";
	}
	else {
		$sql="DELETE FROM mod_actions_inscriptions WHERE id_action='".$id_action."';";
		$del=mysqli_query($mysqli, $sql);
		if(!$del) {
			$msg.="Erreur lors de la suppression des inscriptions&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.="Désinscriptions effectuées.<br />";
		}
	}
}
//==============================================================================
// Définir la liste des inscriptions par défaut
if((isset($id_action))&&(isset($mode))&&(($mode=='inscriptions')||($mode=='presence'))&&(isset($_GET['definir_inscriptions_defaut']))&&(trim($_GET['definir_inscriptions_defaut'])=='y')) {
	check_token();

	if(!saveSetting('mod_actions_inscriptions_defaut_'.$id_categorie, $id_action)) {
		$msg.="Erreur lors de l'enregistrement de 'mod_actions_inscriptions_defaut' à la valeur ".$id_action."<br />";
	}
	else {
		$msg.="Préférence enregistrée.<br />";
	}
}
//==============================================================================
// Inscription de tous les élèves d'une classe
if((isset($id_action))&&(isset($mode))&&($mode=='inscriptions')&&(isset($id_classe))&&(isset($_GET['ajouter_toute_la_classe']))&&($_GET['ajouter_toute_la_classe']=='y')) {
	check_token();

	$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='".$id_classe."' AND login NOT IN (SELECT login_ele FROM mod_actions_inscriptions WHERE id_action='".$id_action."');";
	$res_ele=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_ele)==0) {
		$msg.="La classe choisie est vide ou tous ses élèves sont déjà inscrits.<br />";
	}
	else {
		$cpt_ele=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$lig_ele->login."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'inscription de ".get_nom_prenom_eleve($lig_ele->login)."&nbsp;:<br />".$sql."<br />";
			}
			else {
				$cpt_ele++;
			}
		}

		$msg.=$cpt_ele." élève(s) inscrit(s).<br />";
	}
}

//==============================================================================
// Inscription de tous les élèves précédemment inscrits pour une autre action
if((isset($id_action))&&(isset($mode))&&($mode=='inscriptions')&&(isset($id_action_inscriptions))&&(isset($_GET['ajouter_toute_l_action']))&&($_GET['ajouter_toute_l_action']=='y')) {
	check_token();

	$sql="SELECT DISTINCT login_ele FROM mod_actions_inscriptions WHERE id_action='".$id_action_inscriptions."' AND login_ele NOT IN (SELECT login_ele FROM mod_actions_inscriptions WHERE id_action='".$id_action."');";
	echo "$sql<br />";
	$res_ele=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res_ele)==0) {
		$msg.="L'action modèle choisie est vide ou tous ses élèves sont déjà inscrits.<br />";
	}
	else {
		$cpt_ele=0;
		while($lig_ele=mysqli_fetch_object($res_ele)) {
			$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$lig_ele->login_ele."';";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'inscription de ".get_nom_prenom_eleve($lig_ele->login_ele)."&nbsp;:<br />".$sql."<br />";
			}
			else {
				$cpt_ele++;
			}
		}

		$msg.=$cpt_ele." élève(s) inscrit(s).<br />";
	}
}

//==============================================================================
// Présence d'un élève
if((isset($id_action))&&(isset($mode))&&($mode=='presence')&&(isset($_GET['presence']))&&(trim($_GET['presence'])!='')) {
	check_token();

	$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$_GET['presence']."';";
	//echo "$sql<br />";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="UPDATE mod_actions_inscriptions SET presence='y', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."' WHERE id_action='".$id_action."' AND login_ele='".$_GET['presence']."';";
		//echo "$sql<br />";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg.="Erreur lors du pointage de la présence de ".get_nom_prenom_eleve($_GET['presence'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['presence'])." est présent(e).<br />";
		}
	}
	else {
		$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$_GET['presence']."', presence='y', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$insert=mysqli_query($mysqli, $sql);
		if(!$insert) {
			$msg.="Erreur lors du pointage de la présence de ".get_nom_prenom_eleve($_GET['presence'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['presence'])." est présent(e).<br />";
		}
	}
}

//==============================================================================
// Suppression du pointage de présence d'un élève
if((isset($id_action))&&(isset($mode))&&($mode=='presence')&&(isset($_GET['absence']))&&(trim($_GET['absence'])!='')) {
	check_token();

	$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$_GET['absence']."';";
	//echo "$sql<br />";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)>0) {
		$sql="UPDATE mod_actions_inscriptions SET presence='n', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."' WHERE id_action='".$id_action."' AND login_ele='".$_GET['absence']."';";
		//echo "$sql<br />";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg.="Erreur lors du pointage de l'absence de ".get_nom_prenom_eleve($_GET['absence'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['absence'])." est absent(e).<br />";
		}
	}
	else {
		$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$_GET['absence']."', presence='n', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$insert=mysqli_query($mysqli, $sql);
		if(!$insert) {
			$msg.="Erreur lors du pointage de l'absence de ".get_nom_prenom_eleve($_GET['absence'])."&nbsp;:<br />".$sql."<br />";
		}
		else {
			$msg.=get_nom_prenom_eleve($_GET['absence'])." est absent(e).<br />";
		}
	}
}

//==============================================================================
// Présence d'une sélection d'élèves
if((isset($id_action))&&(isset($mode))&&($mode=='presence')&&(isset($_POST['tab_presence']))) {
	check_token();

	$cpt_presents=0;
	$tab_presence=$_POST['tab_presence'];
	for($loop=0;$loop<count($tab_presence);$loop++) {
		$login_ele=$tab_presence[$loop];

		$sql="SELECT 1=1 FROM mod_actions_inscriptions WHERE id_action='".$id_action."' AND login_ele='".$login_ele."';";
		//echo "$sql<br />";
		$test=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			$sql="UPDATE mod_actions_inscriptions SET presence='y', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."' WHERE id_action='".$id_action."' AND login_ele='".$login_ele."';";
			//echo "$sql<br />";
			$update=mysqli_query($mysqli, $sql);
			if(!$update) {
				$msg.="Erreur lors du pointage de la présence de ".get_nom_prenom_eleve($login_ele)."&nbsp;:<br />".$sql."<br />";
			}
			else {
				$cpt_presents++;
			}
		}
		else {
			$sql="INSERT INTO mod_actions_inscriptions SET id_action='".$id_action."', login_ele='".$login_ele."', presence='y', date_pointage='".strftime("%Y-%m-%d %H:%M:%S")."', login_pointage='".$_SESSION['login']."';";
			//echo "$sql<br />";
			$insert=mysqli_query($mysqli, $sql);
			if(!$insert) {
				$msg.="Erreur lors du pointage de la présence de ".get_nom_prenom_eleve($login_ele)."&nbsp;:<br />".$sql."<br />";
			}
			else {
				$cpt_presents++;
			}
		}
	}

	if($cpt_presents>0) {
		$msg.=$cpt_presents." élève(s) pointé(s) présent(s).<br />";
	}
}

//==============================================================================


$tab_actions_categories=get_tab_actions_categories();
/*
echo "<pre>";
print_r($tab_actions_categories);
echo "</pre>";
*/

// S'il n'y a qu'une catégorie, l'afficher:
if((!isset($id_categorie))&&(count($tab_actions_categories)==1)) {
	foreach($tab_actions_categories as $id_categorie => $categorie) {
		// On ne fait qu'un tour dans la boucle et on sort avec $id_categorie et $categorie affectés.
	}
}

// Configuration du calendrier
$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

//**************** EN-TETE *********************
if (isset($id_categorie)) {
	$categorie=$tab_actions_categories[$id_categorie];

	$titre_page = "Gestion ".$categorie['nom'];
}
else {
	$titre_page = "Gestion des ".$terme_mod_action;
}
require_once("../lib/header.inc.php");
// debug_var();
//**************** FIN EN-TETE *****************

if ((isset($id_categorie))&&(count($tab_actions_categories)>1)) {
	$retour='index.php';
}
elseif ((isset($mode))&&($mode!='')) {
	$retour='index.php';
}
else {
	$retour='../accueil.php';
}
echo "
<p class='bold'>
	<a href='".$retour."'>
		<img src='../images/icons/back.png' alt='Retour' class='back_link'/>
		Retour
	</a>";

// Afficher la liste des catégories
if (!isset($id_categorie)) {
	echo "
</p>

	<h2>".$terme_mod_action."</h2>
	<p>Choisissez une catégorie&nbsp;:</p>
	<ul>";
	foreach($tab_actions_categories as $id_categorie => $categorie) {
		echo "
		<li><a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."'>".$categorie['nom']."</a></li>";
	}
	echo "
	</ul>";
	require("../lib/footer.inc.php");
	die();
}

//==============================================================================
// Ajouter une action dans la catégorie
if((isset($mode))&&($mode=='ajout_action')) {
	$tab_inscriptions_defaut=get_mod_actions_liste_inscriptions_par_defaut($id_categorie);
	echo "
<!--
	 | <a href=''></a>
-->
</p>

	<h2>".$categorie['nom']."</h2>
	<h3>Ajouter une action&nbsp;:</h3>
	<form action='".$_SERVER['PHP_SELF']."' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."
			<table class='boireaus boireaus_alt'>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Nom&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name='nom_action' value=\"".(isset($nom_action) ? $nom_action : $categorie['nom'])."\" onfocus='this.select()' onchange='changement();' />
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Description&nbsp;: </th>
					<td style='text-align:left;'>
						<textarea name='description' cols='50' rows='4' onchange='changement();'>".(isset($description) ? trim($description) : '')."</textarea>
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Date&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name='date_action' id='date_action' value=\"".(isset($date_action) ? $date_action : strftime("%d/%m/%Y"))."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" onchange='changement();' />
						".img_calendrier_js("date_action", "img_bouton_date_action")."
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Heure&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name = 'heure_action' id= 'heure_action' size='5' value = \"".(isset($heure_action) ? $heure_action : strftime("%H:%M"))."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" onchange='changement();' />
						<!--
						".choix_heure('heure_action','div_choix_heure_action', 'return')."
						-->
					</td>
				</tr>
			</table>
			".((count($tab_inscriptions_defaut)>0) ? "<input type='checkbox' name='inscrire_eleves_liste_defaut' id='inscrire_eleves_liste_defaut' value='y' onchange='changement(); checkbox_change(this.id);' /><label for='inscrire_eleves_liste_defaut' id='texte_inscrire_eleves_liste_defaut'> Inscrire les élèves de la liste par défaut <em>(".count($tab_inscriptions_defaut)." élèves)</em>.</label><br />" : "")."
			<input type='hidden' name='id_categorie' value='".$id_categorie."' />
			<input type='hidden' name='valider_ajout_action' value='y' />
			<p><input type='submit' value='Valider' /></p>
		</fieldset>
	</form>
	".js_checkbox_change_style('checkbox_change', 'texte_', 'y');

	require("../lib/footer.inc.php");
	die();
}

//==============================================================================
// Afficher la liste des actions dans la catégorie choisie
if(!isset($id_action)) {

	$tab_actions=array();
	$sql="SELECT * FROM mod_actions_action WHERE id_categorie='".$id_categorie."' ORDER BY date_action, nom;";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)>0) {
		while($lig=mysqli_fetch_assoc($res)) {
			$tab_actions[$lig['id']]=$lig;

			// Récupérer les inscrits?
			$tab_actions[$lig['id']]['eleves']=array();
			$tab_actions[$lig['id']]['presents']=array();
			$sql="SELECT e.nom, e.prenom, mai.* FROM mod_actions_inscriptions mai, 
										eleves e 
									WHERE mai.id_action='".$lig['id']."' AND 
										mai.login_ele=e.login 
									ORDER BY e.nom, e.prenom;";
			$res2=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_assoc($res2)) {
					$tab_actions[$lig['id']]['eleves']=$lig2;
					if($lig2['presence']=='y') {
						$tab_actions[$lig['id']]['presents'][]=$lig2['login_ele'];
					}
				}
			}
		}
	}

	echo "<h2>".$categorie['nom']."</h2>
	<p>Choisissez&nbsp;: <a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&mode=ajout_action'><img src='../images/icons/add.png' class='icone16' title='Ajouter une action' /> Ajouter une action</a></p>";
	if(count($tab_actions)>0) {
		echo "
	<table class='boireaus boireaus_alt resizable sortable'>
		<thead>
			<tr>
				<th>Id</th>
				<th>Nom</th>
				<th>Description</th>
				<th>Date</th>
				<th>Inscription</th>
				<th>Présence</th>
				<th>Supprimer</th>
			</tr>
		</thead>
		<tbody>";
		foreach($tab_actions as $id_action => $action) {
			echo "
			<tr>
				<td>".$id_action."</td>
				<td><a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=afficher' title=\"Consulter l'action ".$terme_mod_action_nettoye." n°$id_action\">".$action['nom']."</a></td>
				<td>".nl2br($action['description'])."</a></td>
				<td>
					<span style='display:none'>".$action['date_action']."</span>
					".formate_date($action['date_action'], 'y')."
				</td>
				<td>
					<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=inscriptions' title=\"Consulter/effectuer les inscriptions\"><img src='../images/icons/add_user.png' class='icone16' /> 
					".count($action['eleves'])." inscrit(s)</a>
				</td>
				<td>
			<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=presence' title=\"Pointer les présents/absents\"><img src='../images/icons/absences_edit.png' class='icone16' /> 
					".count($action['presents'])." présent(s)</a>
				</td>
				<td>
			<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=suppr".add_token_in_url()."' onclick=\"return confirm('Êtes vous sûr de vouloir supprimer ce(tte) ".$terme_mod_action_nettoye."')\" title=\"Supprimer ce(tte) ".$terme_mod_action_nettoye."\"><img src='../images/delete16.png' class='icone16' /></a>
				</td>
			</tr>";
		}
		echo "
	</table>";
	}

	require("../lib/footer.inc.php");
	die();
}

//==============================================================================
// L'action est choisie.
// Récupération des infos associées
$action=array();
$sql="SELECT * FROM mod_actions_action WHERE id_categorie='".$id_categorie."' AND id='".$id_action."' ORDER BY date_action, nom;";
//echo "$sql<br />";
$res=mysqli_query($mysqli, $sql);
if(mysqli_num_rows($res)==0) {
	echo "<p>L'action n°$id_action n'existe pas.<br />
	<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."'>Revenir au choix de l'action</a></p>";

	require("../lib/footer.inc.php");
	die();
}
else {
	$lig=mysqli_fetch_assoc($res);
	$action=$lig;
	$action['eleves']=array();
	$action['eleves_list']=array();
	$action['presents']=array();

	// Récupérer les inscrits?
	$sql="SELECT e.nom, e.prenom, e.elenoet, mai.* FROM mod_actions_inscriptions mai, 
								eleves e 
							WHERE mai.id_action='".$lig['id']."' AND 
								mai.login_ele=e.login 
							ORDER BY e.nom, e.prenom;";
	$res2=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res2)>0) {
		while($lig2=mysqli_fetch_assoc($res2)) {
			$action['eleves'][]=$lig2;
			$action['eleves_list'][]=$lig2['login_ele'];
			if($lig2['presence']=='y') {
				$action['presents'][$lig2['login_ele']]=$lig2;
			}
		}
	}
}

if(!isset($mode)) {
	$mode='afficher';
}

//==============================================================================
// Consulter/modifier l'action choisie
if($mode=='afficher') {
	/*
	echo "<pre>";
	print_r($action);
	echo "</pre>";
	*/

	$tmp_tab=explode(' ', $action['date_action']);
	$date_action=formate_date($tmp_tab[0]);
	$heure_action=preg_replace('/:00$/', '', $tmp_tab[1]);

	echo "<h2>".$action['nom']."</h2>
	<h3>Consulter l'action&nbsp;:</h3>
	<form action='".$_SERVER['PHP_SELF']."' method='post'>
		<fieldset class='fieldset_opacite50'>
			".add_token_field()."
			<table class='boireaus boireaus_alt'>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Nom&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name='nom_action' value=\"".$action['nom']."\" onfocus='this.select()' />
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Description&nbsp;: </th>
					<td style='text-align:left;'>
						<textarea name='description' cols='50' rows='4'>".$action['description']."</textarea>
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Date&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name='date_action' id='date_action' value=\"".$date_action."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
						".img_calendrier_js("date_action", "img_bouton_date_action")."
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Heure&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name = 'heure_action' id= 'heure_action' size='5' value = \"".(isset($heure_action) ? $heure_action : strftime("%H:%M"))."\" onKeyDown=\"clavier_heure(this.id,event);\" AutoComplete=\"off\" />
						<!--
						".choix_heure('heure_action','div_choix_heure_action', 'return')."
						-->
					</td>
				</tr>
				<tr>
					<th>Inscriptions</th>
					<td style='text-align:left;'>
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=inscriptions' title=\"Consulter/effectuer les inscriptions\"><img src='../images/icons/add_user.png' class='icone16' /> ".count($action['eleves'])." élève(s) inscrit(s)</a>
					".((getSettingAOui('active_module_trombinoscopes') && count($action['eleves_list'])>0) ? " <a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."' target='_blank' title=\"Générer un trombinoscope PDF.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>" : "")."
					</td>
				</tr>
				<tr>
					<th>Présence/absence</th>
					<td style='text-align:left;'>
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=presence' title=\"Pointer les présents/absents\"><img src='../images/icons/absences_edit.png' class='icone16' /> 
						".count($action['presents'])." élève(s) pointé(s) présent(s)</a>
						".((getSettingAOui('active_module_trombinoscopes') && count($action['presents'])>0) ? " <a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."&presents_action=y' target='_blank' title=\"Générer un trombinoscope PDF des présents.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>" : "")."
					</td>
				</tr>
			</table>
			<!--input type='hidden' name='id_categorie' value='".$id_categorie."' /-->
			<input type='hidden' name='id_action' value='".$id_action."' />
			<input type='hidden' name='valider_modif_action' value='y' />
			<p><input type='submit' value='Valider' /></p>
		</fieldset>
	</form>";
}
//==============================================================================
elseif($mode=='inscriptions') {
	// Inscrire des élèves dans l'action choisie
	$tmp_tab=explode(' ', $action['date_action']);
	$date_action=formate_date($tmp_tab[0]);
	$heure_action=preg_replace('/:00$/', '', $tmp_tab[1]);

	echo "<h2>".$action['nom']." du ".formate_date($date_action)." à ".$heure_action."</h2>
	<h3>Effectuer les inscriptions&nbsp;:</h3>
	<div style='float:left; min-width:25em; max-width:45em;'>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Nom&nbsp;: </th>
				<td style='text-align:left;'><a href='".$_SERVER['PHP_SELF']."?id_action=".$id_action."&mode=afficher'>".$action['nom']."</a></td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Description&nbsp;: </th>
				<td style='text-align:left;'>".nl2br($action['description'])."</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Date/heure&nbsp;: </th>
				<td style='text-align:left;'>".$date_action." à ".$heure_action."</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Élèves inscrits&nbsp;: ";
	if(count($action['eleves_list'])>0) {
		echo "<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode".(isset($id_classe) ? "&amp;id_classe=".$id_classe : '')."&amp;vider_inscriptions=y".add_token_in_url()."' onclick=\"return confirm('Êtes-vous sûr de vouloir vider toutes les inscriptions de cette ".$terme_mod_action_nettoye."?');\">
							<img src=\"../images/icons/delete.png\" title=\"Supprimer/vider toutes les inscriptions\" alt=\"Supprimer\" />
						</a>";

		echo "
					<div style='text-align:center; padding:2em;'>
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode".(isset($id_classe) ? "&amp;id_classe=".$id_classe : '')."&amp;definir_inscriptions_defaut=y".add_token_in_url()."' title=\"Définir cette liste d'élèves comme liste par défaut pour les futures ".$terme_mod_action_nettoye."s.\">
							<img src='../images/icons/wizard.png' class='icone16' alt='Par défaut' />
						</a>
					</div>";
	}
	echo "</th>
				<td style='text-align:left;'>
					<p>".count($action['eleves_list'])." élève(s)
					".((getSettingAOui('active_module_trombinoscopes') && count($action['eleves_list'])>0) ? " <a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."' target='_blank' title=\"Générer un trombinoscope PDF.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>" : "")."
					</p>
					<p>";
					foreach($action['eleves'] as $cpt_ele => $current_ele) {
						echo "
						<span onmouseover=\"affiche_photo_courante('".nom_photo($current_ele['elenoet'])."')\" onmouseout=\"vide_photo_courante();\">
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode".(isset($id_classe) ? "&amp;id_classe=".$id_classe : '')."&amp;suppr_ele=".$current_ele['login_ele'].add_token_in_url()."'>
							<img src=\"../images/icons/delete.png\" title=\"Supprimer cet élève\" alt=\"Supprimer\" />
						</a>".$current_ele["nom"]." ".$current_ele["prenom"];
						//." ".$current_ele["classe"]
						echo "</span><br />";
					}
					echo "
				</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Présence/absences&nbsp;: </th>
				<td style='text-align:left;'>
					<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=presence' title=\"Pointer les présents/absents\"><img src='../images/icons/absences_edit.png' class='icone16' /> Pointer les présences/absences</a>";
					if(count($action['presents'])>0) {
						echo "<br />
						".count($action['presents'])." élève(s) pointé(s) présent(s)&nbsp;:
						".((getSettingAOui('active_module_trombinoscopes') && count($action['presents'])>0) ? " <a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."&presents_action=y' target='_blank' title=\"Générer un trombinoscope PDF des présents.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>" : "")."
						<br />";
						foreach($action['presents'] as $login_ele => $current_ele) {
							echo "
							<span onmouseover=\"affiche_photo_courante('".nom_photo($current_ele['elenoet'])."')\" onmouseout=\"vide_photo_courante();\">
								".$current_ele["nom"]." ".$current_ele["prenom"]."
							</span><br />";
						}
					}
					echo "
				</td>
			</tr>
		</table>
	</div>

	<div style='float:left; width:10em;'>
		<p class=\"bold\">Liste des classes</p>
		<table>";
	$sql="SELECT id, classe FROM classes ORDER BY classe";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig=mysqli_fetch_object($res)) {
		echo "
			<tr><td style=\"width: 196px;\"><a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_classe=".$lig->id."\">Elèves de la ".$lig->classe."</a></td></tr>";
	}
	echo "
		</table>";

	$sql="SELECT maa.*, COUNT(mai.login_ele) AS effectif 
		FROM mod_actions_action maa, 
			mod_actions_inscriptions mai 
		WHERE maa.id=mai.id_action 
		HAVING COUNT(mai.login_ele)>0 
		ORDER BY maa.date_action;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		echo "
		<p class=\"bold\">Liste des ".$terme_mod_action."s</p>
		<table>";
		while($lig=mysqli_fetch_object($res)) {
			echo "
			<tr><td style=\"width: 196px;\"><a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_action_inscriptions=".$lig->id."\" title=\"Afficher les élèves de ce(tte) ".$terme_mod_action_nettoye."\">".$lig->nom."<span style='font-size:x-small'> (".formate_date($lig->date_action, 'y2').") <span title='Effectif ".$lig->effectif."'>(".$lig->effectif.")</span></span></a></td></tr>";
		}
		echo "
		</table>";
	}
	echo "
	</div>";

	$cpt_ele=0;
	if (isset($id_classe)) {
		echo "
	<div style='float:left; width:20em;'>
		<form action='".$_SERVER['PHP_SELF']."#pointer_presence' method='post' style='text-align:left;'>
			<fieldset class='fieldset_opacite50'>
				".add_token_field()."
				<p class=\"red\">Classe de ".get_nom_classe($id_classe)."&nbsp;:</p>
				<table class=\"aid_tableau\" summary=\"Liste des élèves\">
					<tr class=\"aid_lignepaire\">
						<td>
							<a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_classe=".$id_classe."&amp;ajouter_toute_la_classe=y".add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> Toute la classe
							</a>
						</td>
					</tr>
					<tr>
						<td>Liste des élèves</td>
					</tr>";

				$sql="SELECT DISTINCT e.login, e.id_eleve, nom, prenom, elenoet, sexe 
						FROM j_eleves_classes jec, eleves e 
						WHERE id_classe = '".$id_classe."' AND 
							jec.login = e.login 
						ORDER BY nom, prenom";
				$req_ele = mysqli_query($GLOBALS["mysqli"], $sql) OR die('Erreur dans la requête '.$req_ele.' : '.mysqli_error($GLOBALS["mysqli"]));
				//$cpt_ele=0;
				while($lig_ele=mysqli_fetch_object($req_ele)) {
					if(in_array($lig_ele->login, $action['eleves_list'])) {
						echo "
					<tr class=\"aid_ligneimpaire\">
						<td>
						</td>
					</tr>";
					}
					else {
						echo "
					<tr class=\"aid_lignepaire\">
						<td onmouseover=\"affiche_photo_courante('".nom_photo($lig_ele->elenoet)."')\" onmouseout=\"vide_photo_courante();\">
							<!--
							<a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_classe=".$id_classe."&amp;login_ele=".$lig_ele->login.add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> ".$lig_ele->nom." ".$lig_ele->prenom."
							</a>
							-->
							<input type='checkbox' name='tab_login_ele[]' id='tab_login_ele_".$cpt_ele."' value=\"".$lig_ele->login."\" onchange=\"changement(); checkbox_change(this.id);\" /><label for='tab_login_ele_".$cpt_ele."' id='texte_tab_login_ele_".$cpt_ele."'> ".$lig_ele->nom." ".$lig_ele->prenom."</label>
						</td>
					</tr>";
					}
					$cpt_ele++;
				}
				echo "
				</table>
				<input type='hidden' name='id_classe' value='$id_classe' />
				<input type='hidden' name='id_categorie' value='$id_categorie' />
				<input type='hidden' name='id_action' value='$id_action' />
				<input type='hidden' name='mode' value='$mode' />
				<p><a href=\"javascript:tout_cocher()\">Tout cocher</a> / <a href=\"javascript:tout_decocher()\">Tout décocher</a></p>
				<p><input type='submit' value='Valider' /></p>
				<div id='fixe'><input type='submit' value='Valider' /></div>
			</fieldset>
		</form>
	</div>";
	}

	if (isset($id_action_inscriptions)) {
		echo "
	<div style='float:left; width:20em;'>";
		$sql="SELECT * FROM mod_actions_action WHERE id='".$id_action_inscriptions."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p style='color:red'>".$terme_mod_action." n°".$id_action_inscriptions." inconnu(e).</p>";
		}
		else {
			$lig=mysqli_fetch_object($res);
			echo "
		<form action='".$_SERVER['PHP_SELF']."#pointer_presence' method='post' style='text-align:left;'>
			<fieldset class='fieldset_opacite50'>
				".add_token_field()."
				<p class=\"red\">".$lig->nom."<span style='font-size:x-small'> (".formate_date($lig->date_action, 'y').")</span>&nbsp;:</p>
				<table class=\"aid_tableau\" summary=\"Liste des élèves\">
					<tr class=\"aid_lignepaire\">
						<td>
							<a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_action_inscriptions=".$id_action_inscriptions."&amp;ajouter_toute_l_action=y".add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> Tous les élèves de ce(tte) ".$terme_mod_action."
							</a>
						</td>
					</tr>
					<tr>
						<td>Liste des élèves</td>
					</tr>";

				$sql="SELECT DISTINCT e.login, e.id_eleve, nom, prenom, elenoet, sexe 
						FROM eleves e, 
							mod_actions_inscriptions mai 
						WHERE mai.id_action = '".$id_action_inscriptions."' AND 
							mai.login_ele = e.login 
						ORDER BY nom, prenom";
				$req_ele = mysqli_query($GLOBALS["mysqli"], $sql) OR die('Erreur dans la requête '.$req_ele.' : '.mysqli_error($GLOBALS["mysqli"]));
				//$cpt_ele=0;
				while($lig_ele=mysqli_fetch_object($req_ele)) {
					if(in_array($lig_ele->login, $action['eleves_list'])) {
						echo "
					<tr class=\"aid_ligneimpaire\">
						<td>
						</td>
					</tr>";
					}
					else {
						echo "
					<tr class=\"aid_lignepaire\">
						<td onmouseover=\"affiche_photo_courante('".nom_photo($lig_ele->elenoet)."')\" onmouseout=\"vide_photo_courante();\">
							<!--
							<a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_classe=".$id_classe."&amp;login_ele=".$lig_ele->login.add_token_in_url()."\">
							<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> ".$lig_ele->nom." ".$lig_ele->prenom."
							</a>
							-->
							<input type='checkbox' name='tab_login_ele[]' id='tab_login_ele_".$cpt_ele."' value=\"".$lig_ele->login."\" onchange=\"changement(); checkbox_change(this.id);\" /><label for='tab_login_ele_".$cpt_ele."' id='texte_tab_login_ele_".$cpt_ele."'> ".$lig_ele->nom." ".$lig_ele->prenom."</label>
						</td>
					</tr>";
					}
					$cpt_ele++;
				}
				echo "
				</table>
				<input type='hidden' name='id_action_inscriptions' value='$id_action_inscriptions' />
				<input type='hidden' name='id_categorie' value='$id_categorie' />
				<input type='hidden' name='id_action' value='$id_action' />
				<input type='hidden' name='mode' value='$mode' />
				<p><a href=\"javascript:tout_cocher()\">Tout cocher</a> / <a href=\"javascript:tout_decocher()\">Tout décocher</a></p>
				<p><input type='submit' value='Valider' /></p>
				<div id='fixe'><input type='submit' value='Valider' /></div>
			</fieldset>
		</form>";
		}
		echo "
	</div>";
	}

	echo "
	<div id='div_photo' style='float:left; width:100px;'>
	</div>

	<script type='text/javascript'>
		function affiche_photo_courante(photo) {
			document.getElementById('div_photo').innerHTML=\"<img src='\"+photo+\"' width='150' alt='Photo' />\";
		}

		function vide_photo_courante() {
			document.getElementById('div_photo').innerHTML='';
		}

		function tout_cocher(){
			champs_input=document.getElementsByTagName('input');
			for(i=0;i<champs_input.length;i++){
				type=champs_input[i].getAttribute('type');
				if(type=='checkbox'){
					champs_input[i].checked=true;
					checkbox_change(champs_input[i].getAttribute('id'));
				}
			}
		}

		function tout_decocher(){
			champs_input=document.getElementsByTagName('input');
			for(i=0;i<champs_input.length;i++){

				type=champs_input[i].getAttribute('type');
				if(type=='checkbox'){
					champs_input[i].checked=false;
					checkbox_change(champs_input[i].getAttribute('id'));
				}
			}
		}
		
		".js_checkbox_change_style()."
	</script>";
/*
	if(isset($eleves_list["users"][$e_login]['elenoet'])) {
		echo " onmouseover=\"affiche_photo_courante('".nom_photo($eleves_list["users"][$e_login]['elenoet'])."')\" onmouseout=\"vide_photo_courante();\"";
	}
*/
}
//==============================================================================
elseif($mode=='presence') {
	// Pointer les absences/présences
	// Pouvoir le faire sur un trombi des inscrits, ou dans une liste

	$tmp_tab=explode(' ', $action['date_action']);
	$date_action=formate_date($tmp_tab[0]);
	$heure_action=preg_replace('/:00$/', '', $tmp_tab[1]);

	echo "<h2>".$action['nom']." du ".formate_date($date_action)." à ".$heure_action."</h2>
	<h3>Pointer les présents&nbsp;:</h3>
	<div style='float:left; min-width:25em; max-width:45em;'>
		<table class='boireaus boireaus_alt'>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Nom&nbsp;: </th>
				<td style='text-align:left;'>".$action['nom']."</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Description&nbsp;: </th>
				<td style='text-align:left;'>".nl2br($action['description'])."</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Date/heure&nbsp;: </th>
				<td style='text-align:left;'>".$date_action." à ".$heure_action."</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top; min-width:150px;'>
					Élèves inscrits&nbsp;: 
					<!--
					<p id='div_photo'></p>
					-->
				</th>
				<td style='text-align:left;'>
					<p>
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=inscriptions' title=\"Consulter/effectuer les inscriptions\"><img src='../images/icons/add_user.png' class='icone16' /> ".count($action['eleves_list'])." élève(s) inscrit(s)</a>
						".((getSettingAOui('active_module_trombinoscopes') && count($action['eleves_list'])>0) ? "<a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."' target='_blank' title=\"Générer un trombinoscope PDF.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>" : "")."
					</p>
					<p>";
					foreach($action['eleves'] as $cpt_ele => $current_ele) {
						echo "
						<span onmouseover=\"affiche_photo_courante('".nom_photo($current_ele['elenoet'])."')\" onmouseout=\"vide_photo_courante();\">
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode".(isset($id_classe) ? "&amp;id_classe=".$id_classe : '')."&amp;suppr_ele=".$current_ele['login_ele'].add_token_in_url()."'>
							<img src=\"../images/icons/delete.png\" title=\"Supprimer cet élève\" alt=\"Supprimer\" />
						</a>".$current_ele["nom"]." ".$current_ele["prenom"];
						//." ".$current_ele["classe"]
						echo "</span><br />";
					}
					echo "
				</td>
			</tr>
			<tr>
				<th style='text-align:left; vertical-align:top;'>Présence&nbsp;: </th>
				<td style='text-align:left;'>";
	if(count($action['eleves'])>0) {
		echo "
					<div style='float:right; width:16px; margin:0.3em;' title=\"Notifier les familles par mail de la présence de leur enfant sur ce(tte) ".$terme_mod_action_nettoye."\">
						<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;mode=mail_presence' target='_blank'>
							<img src='../images/icons/mail.png' class='icone16' />
						</a>
					</div>
					<div style='float:right; width:16px; margin:0.3em;' title=\"Générer un trombinoscope des présents sur ce(tte) ".$terme_mod_action_nettoye."\">
						".((getSettingAOui('active_module_trombinoscopes') && count($action['presents'])>0) ? " <a href='../mod_trombinoscopes/trombino_pdf.php?id_action=".$id_action."&presents_action=y' target='_blank' title=\"Générer un trombinoscope PDF des présents.\"><img src='../images/icons/trombinoscope.png' class='icone16' /></a>
					</div>" : "");
	}
	foreach($action['presents'] as $login_ele => $current_ele) {
		echo "
					<span onmouseover=\"affiche_photo_courante('".nom_photo($current_ele['elenoet'])."')\" onmouseout=\"vide_photo_courante();\">
					<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;absence=".$current_ele["login_ele"].add_token_in_url()."#pointer_presence' title=\"Supprimer le pointage de présence&nbsp;: Marquer comme absent.\">
						<img src=\"../images/icons/delete.png\" class='icone16' alt='Suppr' /> 
						".$current_ele["nom"]." ".$current_ele["prenom"]."
					</a>
					</span><br />";
	}
	echo "
				</td>
			</tr>
		</table>
	</div>";

	echo "
	<div style='float:left; margin-left:3em; text-align:center;'>
		<p>
			<strong>Pointer les élèves présents&nbsp;:</strong><br />
			<a href='#pointer_presence'>Cliquer ci-dessous sur les présents avec les trombines.<br />
			<img src='../images/down.png' width='30' height='30' style='vertical-align:middle;' /></a><br />
			ou cochez la liste des présents et validez.
		</p>

		<form action='".$_SERVER['PHP_SELF']."#pointer_presence' method='post' style='text-align:left;'>
			<fieldset class='fieldset_opacite50'>
				".add_token_field();
	foreach($action['eleves'] as $cpt_ele => $current_ele) {
		if(!array_key_exists($current_ele["login_ele"], $action['presents'])) {
			echo "
		<span onmouseover=\"affiche_photo_courante('".nom_photo($current_ele['elenoet'])."')\" onmouseout=\"vide_photo_courante();\">
		<input type='checkbox' name='tab_presence[]' id='tab_presence_".$cpt_ele."' value=\"".$current_ele["login_ele"]."\" onchange=\"changement(); checkbox_change(this.id)\" /><label for='tab_presence_".$cpt_ele."' id='texte_tab_presence_".$cpt_ele."'>".$current_ele["nom"]." ".$current_ele["prenom"]."</label>
		</span><br />";
		}
	}
	echo "
				<input type='hidden' name='id_categorie' value='$id_categorie' />
				<input type='hidden' name='id_action' value='$id_action' />
				<input type='hidden' name='mode' value='$mode' />
				<p><a href=\"javascript:tout_cocher()\">Tout cocher</a> / <a href=\"javascript:tout_decocher()\">Tout décocher</a></p>
				<p><input type='submit' value='Valider' /></p>
				<div id='fixe'><input type='submit' value='Valider' /></div>
			</fieldset>
		</form>
	</div>
	<!--
	-->
	<div id='div_photo' style='float:left; width:100px; margin:0.5em;'></div>

	<div style='clear:both;'>
	<a name='pointer_presence'></a>
	<p class='bold' style='margin-top:1em;'>Élèves non pointés présents&nbsp;:</p>";
	foreach($action['eleves'] as $cpt_ele => $current_ele) {
		if(!array_key_exists($current_ele["login_ele"], $action['presents'])) {
			echo "
		<div style='float:left; width:150px; margin:5px; text-align:center' class='fieldset_opacite50'>
			<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;presence=".$current_ele["login_ele"].add_token_in_url()."#pointer_presence'>
				<img src=\"".nom_photo($current_ele['elenoet'])."\" width='150' alt='Photo' /><br />
				".$current_ele["nom"]." ".$current_ele["prenom"]."
			</a>
		</div>";
		}
	}
	echo "</div>";
	echo js_checkbox_change_style('checkbox_change', 'texte_', "y");

	echo "
	<p style='margin-top:1em; margin-left:4em; text-indent:4em;'><em>NOTES&nbsp;:</em> Lorsque les élèves sont pointés présents, les parents/élèves disposant d'un compte voient le pointage effectué.</p>

	<script type='text/javascript'>
		function affiche_photo_courante(photo) {
			document.getElementById('div_photo').innerHTML=\"<img src='\"+photo+\"' width='150' alt='Photo' />\";
		}

		function vide_photo_courante() {
			document.getElementById('div_photo').innerHTML='';
		}

		function tout_cocher(){
			champs_input=document.getElementsByTagName('input');
			for(i=0;i<champs_input.length;i++){
				type=champs_input[i].getAttribute('type');
				if(type=='checkbox'){
					champs_input[i].checked=true;
					checkbox_change(champs_input[i].getAttribute('id'));
				}
			}
		}

		function tout_decocher(){
			champs_input=document.getElementsByTagName('input');
			for(i=0;i<champs_input.length;i++){

				type=champs_input[i].getAttribute('type');
				if(type=='checkbox'){
					champs_input[i].checked=false;
					checkbox_change(champs_input[i].getAttribute('id'));
				}
			}
		}
	</script>";
}
//==============================================================================
elseif($mode=='mail_presence') {

	$tmp_tab=explode(' ', $action['date_action']);
	$date_action=formate_date($tmp_tab[0]);
	$heure_action=preg_replace('/:00$/', '', $tmp_tab[1]);

	echo "<h2>".$action['nom']." du ".formate_date($date_action)." à ".$heure_action."</h2>
	<h3>Envoi de mails de notification de présence&nbsp;:</h3>";
	foreach($action['presents'] as $login_ele => $current_ele) {
		/*
		echo "<pre>";
		print_r($current_ele);
		echo "</pre><hr/>";
		*/
		echo '
		<p style="margin-bottom:1em;"><strong>'.$current_ele['nom'].' '.$current_ele['prenom'].'&nbsp;:</strong><br />';

		$sujet='Presence de '.$current_ele['nom'].' '.$current_ele['prenom'].' à '.$action['nom'].' du '.formate_date($date_action);
		$message="Bonjour, 

Les organisateurs de l'".$terme_mod_action_nettoye." ".$action['nom'].' du '.formate_date($date_action)." souhaitent confirmer que ".$current_ele['nom'].' '.$current_ele['prenom']." est bien présent(e) lors du pointage effectué.

Cordialement.";

		$tab_resp=get_resp_from_ele_login($current_ele['login_ele']);
		$tab_deja=array();
		foreach($tab_resp as $cpt_resp => $current_resp) {
			echo $current_resp['designation'].'&nbsp;: ';
			if((isset($current_resp['mel']))&&(check_mail($current_resp['mel']))) {
				if(!in_array($current_resp['mel'], $tab_deja)) {
					$tab_deja[]=$current_resp['mel'];
					if(envoi_mail($sujet, $message, $current_resp['mel'])) {
						echo " <img src='../images/icons/mail_succes.png' class='icone16' title=\"Mail envoyé avec succès à ".$current_resp['mel']."\" />";
					}
					else {
						echo " <img src='../images/icons/mail_echec.png' class='icone16' title=\"Échec de l'envoi du mail à ".$current_resp['mel']."\" />";
					}
				}
				else {
					echo " <span title='Adresse mail ".$current_resp['mel']." déjà contactée'>déjà</span> - ";
				}

				if((isset($current_resp['email']))&&(check_mail($current_resp['email']))) {
					if($current_resp['email']!=$current_resp['mel']) {
						$tab_deja[]=$current_resp['email'];
						if(envoi_mail($sujet, $message, $current_resp['email'])) {
							echo " <img src='../images/icons/mail_succes.png' class='icone16' title=\"Mail envoyé avec succès à ".$current_resp['email']."\" />";
						}
						else {
							echo " <img src='../images/icons/mail_echec.png' class='icone16' title=\"Échec de l'envoi du mail à ".$current_resp['email']."\" />";
						}
					}
				}
			}
			else {
				if((isset($current_resp['email']))&&(check_mail($current_resp['email']))) {
					if(!in_array($current_resp['email'], $tab_deja)) {
						$tab_deja[]=$current_resp['email'];
						if(envoi_mail($sujet, $message, $current_resp['email'])) {
							echo " <img src='../images/icons/mail_succes.png' class='icone16' title=\"Mail envoyé avec succès à ".$current_resp['email']."\" />";
						}
						else {
							echo " <img src='../images/icons/mail_echec.png' class='icone16' title=\"Échec de l'envoi du mail à ".$current_resp['email']."\" />";
						}
					}
					else {
						echo " <span title='Adresse mail ".$current_resp['email']." déjà contactée'>déjà</span> - ";
					}
				}
			}


			echo "<br />";
		}
		echo '</p>';
		ob_flush();
		flush();
	}
}
else {
	echo "<p style='color:red'>Mode '$mode' inconnu.</p>";
}
//==============================================================================

echo "
<div style='clear:both'></div>
<pre style='color:red'>
A FAIRE :
// Pouvoir générer une fiche récapitulative: Intitulé de l'action, description, inscrits,...
// Pouvoir effectuer les inscriptions dans la liste alphabétique de tous les élèves de l'établissement (avec des ancres sur l'initiale du nom)
// Pouvoir pointer comme présent en même temps que l'on fait les inscriptions
</pre>
<pre style='color:green'>
FAIT :
// Afficher la liste des catégories		FAIT
// Afficher les actions de la catégorie choisie	FAIT
// Consulter une action dans la catégorie	FAIT
// Ajouter une action dans la catégorie		FAIT
// Inscrire des élèves				FAIT
// Pointer des présents/absents			FAIT
// Supprimer une action				FAIT
// Effectuer l'affichage parent/élève		FAIT
// Pouvoir générer un trombi des inscrits	FAIT
// Pouvoir faire les inscriptions par lots (formulaire) plutôt que élève par élève	FAIT
// Pouvoir envoyer des mails aux parents pour indiquer que l'appel a été fait	FAIT
// Parcourir les mod_actions_inscriptions_defaut_* dans setting et tester si la catégorie existe puis si l'action existe pour faire le ménage	FAIT
// Pouvoir générer un trombi des présents
</pre>";
require("../lib/footer.inc.php");
