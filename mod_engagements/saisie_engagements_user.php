<?php
/*
 *
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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

$niveau_arbo = 1;

// Initialisations files
require_once("../lib/initialisations.inc.php");

// fonctions complémentaires et/ou librairies utiles

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_engagements/saisie_engagements_user.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_engagements/saisie_engagements_user.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Saisie des engagements pour un utilisateur',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=2");
	die();
}

//$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$login_user=isset($_POST['login_user']) ? $_POST['login_user'] : (isset($_GET['login_user']) ? $_GET['login_user'] : NULL);

if(!isset($login_user)) {
	header("Location: ../accueil.php?msg=Utilisateur non choisi.");
	die();
}

$info_user=get_info_user($login_user);
if(count($info_user)==0) {
	header("Location: ../accueil.php?msg=Utilisateur '$login_user' inconnu.");
	die();
}

$engagement_statut=$info_user['statut'];
if(($engagement_statut!='eleve')&&($engagement_statut!='responsable')) {
	header("Location: ../accueil.php?msg=Les engagements ne sont gérés que pour les élèves et responsables.");
	die();
}

//echo "\$engagement_statut=$engagement_statut<br />";
$tab_tous_engagements=get_tab_engagements($engagement_statut);
if($_SESSION['statut']=='administrateur') {
	$tab_engagements=$tab_tous_engagements;
}
elseif($_SESSION['statut']=='cpe') {
	$tab_engagements=get_tab_engagements($engagement_statut, "cpe");
}
elseif($_SESSION['statut']=='scolarite') {
	$tab_engagements=get_tab_engagements($engagement_statut, "scolarite");
}

if(count($tab_tous_engagements['indice'])==0) {
	header("Location: ../accueil.php?msg=Aucun type d engagement n est actuellement défini.");
	die();
}

$nb_tous_engagements=count($tab_tous_engagements['indice']);
//$nb_engagements=count($tab_engagements['indice']);

/*
echo "<pre>";
print_r($tab_tous_engagements);
echo "</pre>";
*/

//debug_var();

$retour=isset($_POST['retour']) ? $_POST['retour'] : (isset($_GET['retour']) ? $_GET['retour'] : NULL);
$retour_eleve=isset($_POST['retour_eleve']) ? $_POST['retour_eleve'] : (isset($_GET['retour_eleve']) ? $_GET['retour_eleve'] : "");
if(!isset($retour)) {
	$url_retour="../accueil.php";
}
elseif(($retour=="modify_resp")&&(acces("/responsables/modify_resp.php", $_SESSION['statut']))&&(isset($info_user['pers_id']))) {
	$url_retour="../responsables/modify_resp.php?pers_id=".$info_user['pers_id'];
}
elseif(($retour=="modify_eleve")&&(acces("/eleves/modify_eleve.php", $_SESSION['statut']))&&($info_user['statut']=="eleve")) {
	$url_retour="../eleves/modify_eleve.php?eleve_login=".$login_user;
}
elseif(($retour=="visu_eleve")&&(acces("/eleves/visu_eleve.php", $_SESSION['statut']))&&($info_user['statut']=="eleve")) {
	$url_retour="../eleves/visu_eleve.php?ele_login=".$login_user;
}
elseif(($retour=="visu_eleve")&&(acces("/eleves/visu_eleve.php", $_SESSION['statut']))&&($info_user['statut']=="responsable")&&($retour_eleve!="")) {
	$url_retour="../eleves/visu_eleve.php?ele_login=".$retour_eleve."&onglet=responsables";
}
else {
	$url_retour="../accueil.php";
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

if(isset($_POST['is_posted'])) {
	check_token();

	$msg="";

	$tab_engagements_user=get_tab_engagements_user($login_user);

	if(isset($_POST['engagement_existant_id_classe'])) {
		$engagement_existant_id_classe=$_POST['engagement_existant_id_classe'];
		foreach($engagement_existant_id_classe as $current_engagement => $current_id_classe) {
			$sql="SELECT * FROM engagements_user WHERE login='$login_user' AND id='$current_engagement';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg.="L'engagement n°$current_engagement n'est pas associé à ".$info_user['civ_denomination']."<br />";
			}
			else {
				$lig=mysqli_fetch_object($res);
				if(!array_key_exists($lig->id_engagement, $tab_engagements['id_engagement'])) {
					$msg.="Vous n'êtes pas autorisé à saisir les engagements de type ".$lig->id_engagement."<br />";
				}
				else {
					if($current_id_classe=='') {
						$sql="UPDATE engagements_user SET id_type='', valeur='$current_id_classe' WHERE login='$login_user' AND id='$current_engagement';";
					}
					else {
						$sql="UPDATE engagements_user SET id_type='id_classe', valeur='$current_id_classe' WHERE login='$login_user' AND id='$current_engagement';";
					}
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						$msg.="Erreur lors de la mise à jour de la classe pour l'engagement n°$current_engagement<br />";
					}
				}
			}
		}
	}

	if(isset($_POST['suppr_engagement_existant'])) {
		$suppr_engagement_existant=$_POST['suppr_engagement_existant'];
		foreach($suppr_engagement_existant as $key => $current_engagement) {
			$sql="SELECT * FROM engagements_user WHERE login='$login_user' AND id='$current_engagement';";
			//echo "$sql<br />";
			$res=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res)==0) {
				$msg.="L'engagement n°$current_engagement n'est pas associé à ".$info_user['civ_denomination']."<br />";
			}
			else {
				$lig=mysqli_fetch_object($res);
				if(!array_key_exists($lig->id_engagement, $tab_engagements['id_engagement'])) {
					$msg.="Vous n'êtes pas autorisé à saisir les engagements de type ".$lig->id_engagement."<br />";
				}
				else {
					$sql="DELETE FROM engagements_user WHERE login='$login_user' AND id='$current_engagement';";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$del) {
						$msg.="Erreur lors de la suppression de l'engagement n°$current_engagement<br />";
					}
				}
			}
		}
	}

	if(isset($_POST['engagement'])) {
		$engagement=$_POST['engagement'];
		$id_classe=$_POST['id_classe'];
		foreach($engagement as $key => $current_id_engagement) {
			if(!array_key_exists($current_id_engagement, $tab_engagements['id_engagement'])) {
				$msg.="Vous n'êtes pas autorisé à saisir les engagements de type ".$current_id_engagement."<br />";
			}
			else {
				if((!isset($id_classe[$key]))||($id_classe[$key]=="")) {
					$sql="INSERT INTO engagements_user SET id_type='', 
										valeur='', 
										login='$login_user',
										id_engagement='$current_id_engagement';";
				}
				else {
					$sql="INSERT INTO engagements_user SET id_type='id_classe', 
										valeur='".$id_classe[$key]."', 
										login='$login_user', 
										id_engagement='$current_id_engagement';";
				}
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'enregistrement de l'engagement de type n°$current_id_engagement<br />";
				}
			}
		}
	}

	if($msg=="") {
		$msg="Modifications effectuées.<br />";
	}

}

// ===================== entete Gepi ======================================//
if($mode=="") {
	$titre_page = "Saisie engagements";
	require_once("../lib/header.inc.php");
}
// ===================== fin entete =======================================//

//debug_var();

if($mode=="") {
	echo "<p class='bold'><a href='$url_retour'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	if(acces('/mod_engagements/saisie_engagements.php', $_SESSION['statut'])) {
		echo " | <a href='saisie_engagements.php'>Saisir les engagements par classe</a>";
	}
	echo "</p>";
}

$sql="SELECT DISTINCT c.* FROM j_eleves_classes jec, classes c WHERE (c.id=jec.id_classe) ORDER BY c.classe;";
$call_classes=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_classes=mysqli_num_rows($call_classes);
if($nb_classes==0){
	echo "<p>Aucune classe avec élève affecté n'a été trouvée.</p>\n";
	if($mode=="") {
		require("../lib/footer.inc.php");
	}
	die();
}

$tab_classe=array();
while($lig_clas=mysqli_fetch_object($call_classes)) {
	$tab_classe[$lig_clas->id]=$lig_clas->classe;
}

$tab_engagements_user=get_tab_engagements_user($login_user);
/*
echo "<pre>";
print_r($tab_engagements_user);
echo "</pre>";
*/
echo "
<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<input type='hidden' name='is_posted' value='1' />
	<input type='hidden' name='login_user' value='$login_user' />
	<input type='hidden' name='retour' value='$retour' />
	<input type='hidden' name='retour_eleve' value='$retour_eleve' />
	<input type='hidden' name='mode' value='$mode' />
	".add_token_field();

if(count($tab_engagements_user)>0) {
	echo "
	<p class='bold'>Engagements existants pour ".$info_user['civ_denomination']."</p>
	<table class='boireaus boireaus_alt' summary='Engagements'/>
		<tr>
			<th>Engagement</th>
			<th>Classe</th>
			<th>Supprimer</th>
		</tr>";
	for($loop2=0;$loop2<count($tab_engagements_user['indice']);$loop2++) {
		$current_engagement=$tab_engagements_user['indice'][$loop2]['id'];
		echo "
		<tr>
			<td>".$tab_engagements_user['indice'][$loop2]['nom_engagement']."</td>
			<td>";
		if($tab_engagements_user['indice'][$loop2]['type']=='id_classe') {
			echo "
				<select name='engagement_existant_id_classe[$current_engagement]'>
					<option value=''>---</option>";
			foreach($tab_classe as $id_classe => $classe) {
				$selected="";
				if(($tab_engagements_user['indice'][$loop2]['id_type']=='id_classe')&&($tab_engagements_user['indice'][$loop2]['valeur']==$id_classe)) {
					$selected=" selected";
				}
				echo "
					<option value='$id_classe'$selected>$classe</option>";
			}
			echo "
				</select>";
		}
		echo "
			</td>
			<td><input type='checkbox' name='suppr_engagement_existant[]' value='$current_engagement' /></td>
		</tr>";
	}
	echo "
	</table>
	<br />";
}

if(count($tab_engagements['indice'])>0) {
	echo "
	<p class='bold'>Saisir des engagements pour ".$info_user['civ_denomination']."</p>
	<table class='boireaus boireaus_alt' summary='Engagements'/>
		<tr>
			<th>Engagement</th>
			<th>Etat</th>
			<th>Classe</th>
		</tr>";

	for($loop=0;$loop<$nb_tous_engagements;$loop++) {
		if(($_SESSION['statut']=='administrateur')||
		(($_SESSION['statut']=='cpe')&&($tab_engagements['indice'][$loop]['SaisieCpe']=='yes'))||
		(($_SESSION['statut']=='scolarite')&&($tab_engagements['indice'][$loop]['SaisieScol']=='yes'))
		) {
			echo "
		<tr>
			<td>".$tab_tous_engagements['indice'][$loop]['nom']."</td>";

			$current_id_engagement=$tab_tous_engagements['indice'][$loop]['id'];

			echo "
			<td>
				<input type='checkbox' name='engagement[$loop]' id='engagement_".$loop."' value=\"".$current_id_engagement."\" />
			</td>
			<td>";
			if($tab_tous_engagements['indice'][$loop]['type']=='id_classe') {
				echo "
				<select name='id_classe[$loop]'>
					<option value=''>---</option>";
				foreach($tab_classe as $id_classe => $classe) {
					echo "
					<option value='$id_classe'>$classe</option>";
				}
				echo "
				</select>";
			}
			echo "
			</td>
		</tr>";
		}
	}

	echo "
	</table>";
}
echo "

	<p><input type='submit' value='Valider' /></p>
</form>";

require_once("../lib/footer.inc.php");

?>
