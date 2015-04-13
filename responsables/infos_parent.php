<?php

/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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
if ($resultat_session == "c") {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == "0") {
    header("Location: ../logout.php?auto=1");
    die();
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=2");
    die();
}

// ===================== entete Gepi ======================================//
$titre_page = "Informations";
require_once("../lib/header.inc.php");
// ===================== fin entete =======================================//

echo "<div class='norme'>
	<p class='bold'>
		<a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
	</p>
</div>

<p>Cette page est destinée à récapituler les informations saisies vous concernant et concernant les élèves/enfants dont vous êtes responsable.<br />
<strong>En cas d'erreur</strong>, merci de contacter le secrétariat de l'établissement, par courrier à l'adresse&nbsp;:<br />
".getSettingValue('gepiSchoolName')."
".((getSettingValue('gepiSchoolAdress1')!="") ? ", ".getSettingValue('gepiSchoolAdress1') : "")."
".((getSettingValue('gepiSchoolAdress2')!="") ? ", ".getSettingValue('gepiSchoolAdress2') : "")."
".((getSettingValue('gepiSchoolZipCode')!="") ? ", ".getSettingValue('gepiSchoolZipCode') : "")."
".((getSettingValue('gepiSchoolCity')!="") ? ", ".getSettingValue('gepiSchoolCity') : "")."<br />
ou par téléphone au ".getSettingValue('gepiSchoolTel')."</p>

<p class='bold' style='margin-top:2em;'>Voici les informations vous concernant personnellement&nbsp;:</p>";

$sql="SELECT rp.* FROM resp_pers rp WHERE login='".$_SESSION['login']."';";
$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
//echo "$sql<br />";
if(mysqli_num_rows($res_resp)==0) {
	echo "<p class='red'>Vous n'avez pas été trouvé dans la table 'resp_pers'&nbsp;???<br />
C'est une anomalie.<br />
Veuillez le signaler à l'établissement.</p>
<p><br /></p>\n";

	require_once("../lib/footer.inc.php");
	die();
}

$lig=mysqli_fetch_object($res_resp);
echo "
<div style='margin-left:2em;'>
	<table class='boireaus boireaus_alt boireaus_th_left' summary='Tableau de vos informations personnelles'>
		<tr>
			<th>Nom</th>
			<td>".$lig->nom."</td>
		</tr>
		<tr>
			<th>Prénom</th>
			<td>".$lig->prenom."</td>
		</tr>
		<tr>
			<th>Civilité</th>
			<td>".$lig->civilite."</td>
		</tr>
		<tr>
			<th>Tél.personnel</th>
			<td>".$lig->tel_pers."</td>
		</tr>
		<tr>
			<th>Tél.portable</th>
			<td>".$lig->tel_port."</td>
		</tr>
		<tr>
			<th>Tél.professionnel</th>
			<td>".$lig->tel_prof."</td>
		</tr>
		<tr>
			<th>Email (*)</th>
			<td>".$lig->mel."</td>
		</tr>";

$sql="SELECT * FROM resp_adr WHERE adr_id='".$lig->adr_id."';";
$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_adr)==0) {
	echo "
		<tr>
			<th>Adresse</th>
			<td style='color:red'>Aucune adresse n'est enregistrée.</td>
		</tr>";
}
else {
	$lig_adr=mysqli_fetch_object($res_adr);

	if($lig_adr->adr1!='') {
		echo "		<tr><th>Ligne 1 adresse:</th><td>".$lig_adr->adr1."</td></tr>\n";
	}
	if($lig_adr->adr2!='') {
		echo "		<tr><th>Ligne 2 adresse:</th><td>".$lig_adr->adr2."</td></tr>\n";
	}
	if($lig_adr->adr3!='') {
		echo "		<tr><th>Ligne 3 adresse:</th><td>".$lig_adr->adr3."</td></tr>\n";
	}
	if($lig_adr->adr4!='') {
		echo "		<tr><th>Ligne 4 adresse:</th><td>".$lig_adr->adr4."</td></tr>\n";
	}
	if($lig_adr->cp!='') {
		echo "		<tr><th>Code postal:</th><td>".$lig_adr->cp."</td></tr>\n";
	}
	if($lig_adr->commune!='') {
		echo "		<tr><th>Commune:</th><td>".$lig_adr->commune."</td></tr>\n";
	}
	if($lig_adr->pays!='') {
		echo "		<tr><th>Pays:</th><td>".$lig_adr->pays."</td></tr>\n";
	}
}

echo "
	</table>

	<p style='margin-top:2em;'>(*) L'adresse email définie dans la table 'resp_pers' peut différer de l'adresse mail définie dans 'Gérer mon compte'.<br />
	Cette éventuelle différence ne devrait être que temporaire (<em>le temps que le secrétariat de l'établissement effectue la synchronisation de ces adresses</em>).</p>
</div>";

echo "<p class='bold' style='margin-top:2em;'>Enfants/élèves dont vous êtes responsable légal&nbsp;:</p>
<!--div style='margin-left:2em;'-->";

$sql="(SELECT e.* FROM eleves e,
					responsables2 r
				WHERE e.ele_id=r.ele_id AND
					r.pers_id='".$lig->pers_id."' AND
				(r.resp_legal='1' OR r.resp_legal='2') ORDER BY e.nom,e.prenom)";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_adr)==0) {
	echo "<p style='color:red'>Vous n'êtes responsable légal d'aucun élève enregistré dans la base.</p>";
	echo "</div>\n";
	require_once("../lib/footer.inc.php");
	die();
}

while($lig_ele=mysqli_fetch_object($res_ele)) {
	$tab_clas=get_class_from_ele_login($lig_ele->login);

	$ligne_login="";
	$sql="SELECT etat, auth_mode FROM utilisateurs WHERE statut='eleve' AND etat='actif' AND login='$lig_ele->login';";
	$test_compte=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_compte)>0) {
		$lig_user=mysqli_fetch_object($test_compte);
		$ligne_login="
		<tr>
			<th>Login</th>
			<td>
				".$lig_ele->login."<br />
				(<em>compte <span style='color:".(($lig_user->etat=='actif') ? "green' title='Le compte peut se connecter" : "red' title='Le compte ne peut pas se connecter")."'>".$lig_user->etat."</span></em>)
			</td>
		</tr>";
	}

	$ligne_lieu_naissance="";
	if(getSettingAOui('ele_lieu_naissance')) {
		$ligne_lieu_naissance="
		<tr>
			<th>Lieu de naissance</th>
			<td>".get_commune($lig_ele->lieu_naissance,1)."</td>
		</tr>";
	}

	$ligne_tel_pers_ele="";
	if(getSettingAOui('ele_tel_pers')) {
		$ligne_tel_pers_ele="
			<tr>
				<th>Tél.personnel</th>
				<td>".$lig_ele->tel_pers."</td>
			</tr>";
	}

	$ligne_tel_pers_port="";
	if(getSettingAOui('ele_tel_port')) {
		$ligne_tel_pers_port="
			<tr>
				<th>Tél.portable</th>
				<td>".$lig_ele->tel_port."</td>
			</tr>";
	}

	$ligne_tel_pers_prof="";
	if(getSettingAOui('ele_tel_prof')) {
		$ligne_tel_pers_prof="
			<tr>
				<th>Tél.professionnel</th>
				<td>".$lig_ele->tel_prof."</td>
			</tr>";
	}

	$ligne_regime="";
	$sql="SELECT * FROM j_eleves_regime WHERE login='$lig_ele->login';";
	$res_reg=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_reg)>0) {
		$lig_reg=mysqli_fetch_object($res_reg);
		$ligne_regime="
			<tr>
				<th>Régime</th>
				<td>";
		if($lig_reg->regime == "d/p") {$ligne_regime.="Demi-pensionnaire";}
		elseif ($lig_reg->regime == "ext.") {$ligne_regime.="Externe";}
		elseif ($lig_reg->regime == "int.") {$ligne_regime.="Interne";}
		elseif ($lig_reg->regime == "i-e") {
			$ligne_regime.="Interne&nbsp;externé";
			if (my_strtoupper($tab_ele['sexe'])!= "F") {$ligne_regime.="e";}
		}
		$ligne_regime.="</td>
			</tr>

			<tr>
				<th>Redoublant</th>
				<td>".(($lig_reg->doublant == "R") ? "Oui" : "Non")."</td>
			</tr>";
	}

	echo "
	<div style='float:left; width:25em; margin-left:2em;'>
		<table class='boireaus boireaus_alt boireaus_th_left' summary='Tableau de vos informations personnelles'>
".$ligne_login."
			<tr>
				<th>Nom</th>
				<td>".$lig_ele->nom."</td>
			</tr>
			<tr>
				<th>Prénom</th>
				<td>".$lig_ele->prenom."</td>
			</tr>
			<tr>
				<th>Genre</th>
				<td>".(($lig_ele->sexe=='F') ? "féminin" : "masculin")."</td>
			</tr>
			<tr>
				<th>Né(e) le</th>
				<td>".formate_date($lig_ele->naissance)."</td>
			</tr>".$ligne_lieu_naissance.$ligne_tel_pers_ele.$ligne_tel_pers_port.$ligne_tel_pers_prof."
			<tr>
				<th>Email (*)</th>
				<td>".$lig_ele->email."</td>
			</tr>
			<tr>
				<th>Classe</th>
				<td>".$tab_clas['liste_nbsp']."</td>
			</tr>".$ligne_regime."
		</table>
	</div>";
}

echo "<div style='clear:both'></div>
<p><br /></p>\n";
require_once("../lib/footer.inc.php");
?>
