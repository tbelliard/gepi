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

// A ce stade, si un id_classe est choisi/correct, c'est forcément un nombre entier.

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
			$msg.="Action enregistrée (".strftime('%d/%m/%Y à %H:%M:%S').").<br />";
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
						<input type='text' name='nom_action' value=\"".(isset($nom_action) ? $nom_action : $categorie['nom'])."\" onfocus='this.select()' />
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Description&nbsp;: </th>
					<td style='text-align:left;'>
						<textarea name='description' cols='50' rows='4'>".(isset($description) ? trim($description) : '')."</textarea>
					</td>
				</tr>
				<tr>
					<th style='text-align:left; vertical-align:top;'>Date&nbsp;: </th>
					<td style='text-align:left;'>
						<input type='text' name='date_action' id='date_action' value=\"".(isset($date_action) ? $date_action : strftime("%d/%m/%Y"))."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />
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
			</table>
			<input type='hidden' name='id_categorie' value='".$id_categorie."' />
			<input type='hidden' name='valider_ajout_action' value='y' />
			<p><input type='submit' value='Valider' /></p>
		</fieldset>
	</form>";

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
			$sql="SELECT e.nom, e.prenom, mai.* FROM mod_actions_inscriptions mai, 
										eleves e 
									WHERE mai.id_action='".$lig['id']."' AND 
										mai.login_ele=e.login 
									ORDER BY e.nom, e.prenom;";
			$res2=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_assoc($res2)) {
					$tab_actions[$lig['id']]['eleves']=$lig2;
				}
			}
		}
	}

	echo "<h2>".$categorie['nom']."</h2>
	<p>Choisissez&nbsp;: <a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&mode=ajout_action'><img src='../images/icons/add.png' class='icone16' title='Ajouter une action' /></a></p>";
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
			<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=inscriptions' title=\"Consulter/effectuer les inscriptions\"><img src='../images/icons/add_user.png' class='icone16' /></a><br />
				</td>
				<td>
			<a href='".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&id_action=".$id_action."&mode=presence' title=\"Pointer les présents/absents\"><img src='../images/icons/absences_edit.png' class='icone16' /></a><br />
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
						".count($action['presents'])." élève(s) pointé(s) présent(s).</a>
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
				<th style='text-align:left; vertical-align:top;'>Élèves inscrits&nbsp;: </th>
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
						".count($action['presents'])." élève(s) pointé(s) présent(s)&nbsp;:<br />";
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
		</table>
	</div>";

	if (isset($id_classe)) {
		echo "
	<div style='float:left; width:20em;'>
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
					<a href=\"".$_SERVER['PHP_SELF']."?id_categorie=".$id_categorie."&amp;id_action=".$id_action."&amp;mode=$mode&amp;id_classe=".$id_classe."&amp;login_ele=".$lig_ele->login.add_token_in_url()."\">
					<img src=\"../images/icons/add_user.png\" alt=\"Ajouter\" title=\"Ajouter\" /> ".$lig_ele->nom." ".$lig_ele->prenom."
					</a>
				</td>
			</tr>";
			}
		}
		echo "
		</table>
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
// Pouvoir faire les inscriptions par lots (formulaire) plutôt que élève par élève
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
</pre>";
require("../lib/footer.inc.php");
