<?php
/*
 *
 *
 * Copyright 2010-2017 Josselin Jacquard, Stephane Boireau
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

$niveau_arbo = 2;
// Initialisations files
//include("../../lib/initialisationsPropel.inc.php");
require_once("../../lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs2/admin/admin_droits.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_abs2/admin/admin_droits.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Abs2: Droits accès aux pages admin',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../../logout.php?auto=1");
	die();
}

$tab_url=array();
$tab_url[]="/mod_abs2/admin/admin_types_absences.php";
$tab_url[]="/mod_abs2/admin/admin_motifs_absences.php";
$tab_url[]="/mod_abs2/admin/admin_lieux_absences.php";
$tab_url[]="/mod_abs2/admin/admin_justifications_absences.php";

$tab_pages=array();
$tab_pages["/mod_abs2/admin/admin_types_absences.php"]="Types d'absence";
$tab_pages["/mod_abs2/admin/admin_motifs_absences.php"]="Motifs d'absence";
$tab_pages["/mod_abs2/admin/admin_lieux_absences.php"]="Lieux d'absence";
$tab_pages["/mod_abs2/admin/admin_justifications_absences.php"]="Justifications d'absence";

//debug_var();

$msg="";

if(isset($_POST['enregistrer_droits'])) {
	check_token();

	$sql="TRUNCATE a_droits;";
	//echo "$sql<br />";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);

	$tab_droits=array();
	$cpt=0;
	foreach($tab_pages as $url => $intitule) {

		$consultation=isset($_POST["consultation_".$cpt]) ? $_POST["consultation_".$cpt] : array();
		foreach($consultation as $login_user => $valeur_droit) {
			$tab_droits[$login_user][$url]["consultation"]=$valeur_droit;
			// Initialisation
			$tab_droits[$login_user][$url]["saisie"]="n";
		}

		$saisie=isset($_POST["saisie_".$cpt]) ? $_POST["saisie_".$cpt] : array();
		foreach($saisie as $login_user => $valeur_droit) {
			$tab_droits[$login_user][$url]["saisie"]=$valeur_droit;
			if($valeur_droit=="y") {
				// Initialisation: Valeur forcée si le droit de consultation est donné
				$tab_droits[$login_user][$url]["consultation"]="y";
			}
		}

		$cpt++;
	}

	$nb_reg=0;
	foreach($tab_droits as $login_user => $current_tab) {
		foreach($current_tab as $url => $current_page) {
			if(($current_page["consultation"]=="y")||($current_page["saisie"]=="y")) {
				$sql="INSERT INTO a_droits SET page='".$url."', login='".$login_user."', consultation='".$current_page["consultation"]."', saisie='".$current_page["saisie"]."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'insertion des droits sur '".$url."' pour '".$login_user."'<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}
	}
	if($nb_reg>0) {
		$msg.="Droits enregistrés <em>(".strftime("le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
	}

	$consultation_add=isset($_POST["consultation_add"]) ? $_POST["consultation_add"] : array();
	$saisie_add=isset($_POST["saisie_add"]) ? $_POST["saisie_add"] : array();
	if((isset($_POST["login_add"]))&&($_POST["login_add"]!="")&&
		((isset($consultation_add))||(isset($saisie_add)))) {
		$nb_ajout=0;
		for($loop=0;$loop<count($tab_pages);$loop++) {

			$valeur_saisie="n";
			if(isset($saisie_add[$loop])) {
				$valeur_saisie="y";
				$valeur_consultation="y";
			}
			else {
				$valeur_consultation="n";
				if(isset($consultation_add[$loop])) {
					$valeur_consultation="y";
				}
			}

			if(($valeur_consultation=="y")||($valeur_saisie=="y")) {
				$sql="INSERT INTO a_droits SET page='".$tab_url[$loop]."', login='".$_POST["login_add"]."', consultation='".$valeur_consultation."', saisie='".$valeur_saisie."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'insertion du droit sur '".$tab_url[$loop]."'<br />";
				}
				else {
					$nb_ajout++;
				}
			}
		}
		if($nb_ajout>0) {
			$msg.="Droits ajoutés pour un nouvel utilisateur.<br />";
		}
	}
}

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Abs2: Droits administration";
//echo "<div class='noprint'>\n";
require_once("../../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

if(acces_consultation_admin_abs2("/mod_abs2/admin/index.php")) {
	echo "<p class='bold'><a href='./index.php'><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
elseif(acces("/mod_abs2/index.php", $_SESSION["statut"])) {
	echo "<p class='bold'><a href='../index.php'><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
else {
	echo "<p class='bold'><a href='../../accueil.php'><img src='../../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}

echo "<h2>Gérer les accès non-administrateur à des pages d'administration Absences 2</h2>";

$tab_user=array();
//$sql="SELECT u.login, u.nom, u.prenom, u.civilite, u.statut FROM utilisateurs u WHERE u.login NOT IN (SELECT DISTINCT login FROM a_droits) AND u.statut!='eleve' AND u.statut!='responsable' ORDER BY u.nom, u.prenom";
$sql="SELECT u.login, u.nom, u.prenom, u.civilite, u.statut FROM utilisateurs u WHERE u.login NOT IN (SELECT DISTINCT login FROM a_droits) AND u.statut!='eleve' AND u.statut!='responsable' AND u.statut!='professeur' ORDER BY u.nom, u.prenom";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while($lig=mysqli_fetch_assoc($res)) {
		$tab_user[$lig["login"]]=$lig;
	}
}

$tab_user_avec_droits=array();
//$sql="SELECT DISTINCT u.login, u.nom, u.prenom, u.civilite, u.statut FROM a_droits ad, utilisateurs u WHERE ad.login=u.login ORDER BY u.nom, u.prenom";
$sql="SELECT DISTINCT ad.*, u.nom, u.prenom, u.civilite, u.statut FROM a_droits ad, utilisateurs u WHERE ad.login=u.login ORDER BY u.nom, u.prenom";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
$nb_user_avec_droits=mysqli_num_rows($res);
if($nb_user_avec_droits==0) {
	echo "<p>Aucun utilisateur non administrateur ne s'est vu créditer de droits.</p>";
}
else {
	while($lig=mysqli_fetch_object($res)) {
		$tab_user_avec_droits[$lig->login]["login"]=$lig->login;
		$tab_user_avec_droits[$lig->login]["nom"]=$lig->nom;
		$tab_user_avec_droits[$lig->login]["prenom"]=$lig->prenom;
		$tab_user_avec_droits[$lig->login]["civilite"]=$lig->civilite;
		$tab_user_avec_droits[$lig->login]["statut"]=$lig->statut;
		$tab_user_avec_droits[$lig->login]["page"][$lig->page]["consultation"]=$lig->consultation;
		$tab_user_avec_droits[$lig->login]["page"][$lig->page]["saisie"]=$lig->saisie;
	}
	/*
	echo "<pre>";
	print_r($tab_user_avec_droits);
	echo "</pre>";
	*/
}

echo "
<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='enregistrer_droits' value='y' />
		<table>
			<tr>
				<td>
					<p>Donner des droits à un nouvel utilisateur&nbsp;: 
				</td>
				<td>
					<select name='login_add'>
						<option value=''>---</option>";
foreach($tab_user as $login_user => $current_user) {
	echo "
						<option value='".$current_user["login"]."' title='".$current_user["login"]."'>".$current_user["nom"]." ".$current_user["prenom"]." (".$current_user["statut"].")</option>";
}
echo "
					</select></p>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<table class='boireaus boireaus_alt'>
						<thead>
							<tr>
								<th>Page</th>
								<th>Consultation</th>
								<th>Modification</th>
							</tr>
						</thead>
						<tbody>";
$cpt=0;
foreach($tab_pages as $url => $intitule) {
	echo "
							<tr>
								<th>".$intitule."</th>
								<th><input type='checkbox' name='consultation_add[$cpt]' value='y' /></th>
								<th><input type='checkbox' name='saisie_add[$cpt]' value='y' /></th>
							</tr>";
	$cpt++;
}
echo "
						</tbody>
					</table>
				</td>
			</tr>
		</table>
		<input type='submit' value='Valider' />
		</p>";

if($nb_user_avec_droits>0) {
	echo "
		<p style='margin-top:2em;'>Des droits existent pour les utilisateurs suivants&nbsp;:</p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th rowspan='2'>Login</th>
					<th rowspan='2'>Nom prénom</th>
					<th rowspan='2'>Statut</th>";
	foreach($tab_pages as $url => $intitule) {
		echo "
					<th colspan='2'>".$intitule."</th>";
	}
	echo "
				</tr>
				<tr>";
	foreach($tab_pages as $url => $intitule) {
		echo "
					<th>Consultation</th>
					<th>Modification</th>";
	}
	echo "
				</tr>
			</thead>
			<tbody>";
	foreach($tab_user_avec_droits as $login_user => $current_user) {
		echo "
				<tr>
					<td>".$current_user["login"]."</td>
					<td>".$current_user["nom"]." ".$current_user["prenom"]."</td>
					<td>".$current_user["statut"]."</td>";
		$cpt=0;
		foreach($tab_pages as $url => $intitule) {
			$checked_consultation="";
			$checked_saisie="";
			if((isset($current_user["page"][$url]["consultation"]))&&($current_user["page"][$url]["consultation"]=="y")) {
				$checked_consultation=" checked";
			}
			if((isset($current_user["page"][$url]["saisie"]))&&($current_user["page"][$url]["saisie"]=="y")) {
				$checked_saisie=" checked";
			}
			echo "
					<td><input type='checkbox' name='consultation_".$cpt."[".$login_user."]' value='y'".$checked_consultation." /></td>
					<td><input type='checkbox' name='saisie_".$cpt."[".$login_user."]' value='y'".$checked_saisie." /></td>";
			$cpt++;
		}
		echo "
				</tr>";
	}
	echo "
			</tbody>
		</table>
		<input type='submit' value='Valider' />";
}
echo "
	</fieldset>
</form>";




require("../../lib/footer.inc.php");
?>
