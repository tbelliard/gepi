<?php
/*
 *
 * Copyright 2001-2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");

// Resume session
//$resultat_session = resumeSession();
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/mod_orientation/consulter_orientation.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/mod_orientation/consulter_orientation.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Consultation des voeux et orientations proposées',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

/*
$acces="n";
if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieVoeuxScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieVoeuxCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieVoeuxPP'))&&(is_pp($_SESSION['login'])))) {
	$acces="y";
}

if($acces=="n") {
	header("Location: ../accueil.php?msg=Accès à la saisie des voeux non autorisé");
	die();
}
*/

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

/*
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";
*/
//$themessage = 'Des modifications n ont pas été validées. Voulez-vous vraiment quitter sans enregistrer ?';
//================================
$titre_page = "Consultation voeux et orientation";
require_once("../lib/header.inc.php");
//================================

//debug_var();

echo "<p class='bold'>
	<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";

$parametres_liens="";
if(isset($id_classe)) {
	$parametres_liens="?id_classe=".$id_classe;
	echo "
 | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir une autre classe</a>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieVoeuxScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieVoeuxCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieVoeuxPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
 | <a href='saisie_voeux.php".$parametres_liens."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les voeux</a>";
}

if(($_SESSION['statut']=='administrateur')||
(($_SESSION['statut']=='scolarite')&&(getSettingAOui('OrientationSaisieOrientationScolarite')))||
(($_SESSION['statut']=='cpe')&&(getSettingAOui('OrientationSaisieOrientationCpe')))||
(($_SESSION['statut']=='professeur')&&(getSettingAOui('OrientationSaisieOrientationPP'))&&(is_pp($_SESSION['login'])))) {
	echo "
 | <a href='saisie_orientation.php".$parametres_liens."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les orientations proposées</a>";
}

if(acces_saisie_type_orientation()) {
	echo "
 | <a href='saisie_types_orientation.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Saisir les types d'orientations</a>";
}
/*
if(acces("/mod_orientation/consulter_orientation.php", $_SESSION['statut'])) {
	echo "
 | <a href='consulter_orientation.php".$parametres_liens."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Consulter les voeux et orientations proposées</a>";
}
*/
echo "
</p>

<h2>Consultation des voeux d'orientation et orientation proposée".(isset($id_classe) ? " (".get_nom_classe($id_classe).")" : "")."</h2>

<p>Ce module est destiné à gérer les voeux d'orientation des élèves et les orientations proposées par le conseil de classe.</p>";

if(!isset($id_classe)) {
	/*
	if($_SESSION['statut']=='professeur') {
	$sql="SELECT DISTINCT c.id, c.classe FROM classes c, j_eleves_classes jec, j_eleves_professeurs jep WHERE c.id=jec.id_classe AND jep.id_classe=jec.id_classe AND jec.login=jep.login AND jep.professeur='".$_SESSION['login']."' ORDER BY c.classe;";
	}
	else {
	*/
		$sql=retourne_sql_mes_classes();
	//}
	$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_clas)==0) {
		echo "<p style=color:red'>Aucune classe ne vous est associée.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_classe_o=array();
	$sql="SELECT DISTINCT id_classe FROM o_mef om, j_eleves_classes jec, eleves e WHERE om.affichage='y' AND e.mef_code=om.mef_code AND e.login=jec.login;";
	//echo "$sql<br />";
	$res_clas_o=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_clas_o=mysqli_fetch_object($res_clas_o)) {
		$tab_classe_o[]=$lig_clas_o->id_classe;
	}

	if(mysqli_num_rows($res_clas)==1) {
		$lig_clas=mysqli_fetch_object($res_clas);
		$id_classe=$lig_clas->id;
		if(!in_array($id_classe, $tab_classe_o)) {
			echo "<p style=color:red'>Aucune classe avec MEF associé à un niveau d'orientation ne vous est associée.</p>";
			require("../lib/footer.inc.php");
			die();
		}
	}
	else {
		$tab_txt=array();
		$tab_lien=array();
		while($lig_clas=mysqli_fetch_object($res_clas)) {
			if(in_array($lig_clas->id, $tab_classe_o)) {
				$tab_lien[] = $_SERVER['PHP_SELF']."?id_classe=".$lig_clas->id;
				$tab_txt[] = $lig_clas->classe;
			}
		}

		if(count($tab_lien)==0) {
			echo "<p style=color:red'>Aucune classe avec MEF associé à un niveau d'orientation ne vous est associée.</p>";
			require("../lib/footer.inc.php");
			die();
		}

		$nbcol=3;
		tab_liste($tab_txt,$tab_lien,$nbcol);

		require("../lib/footer.inc.php");
		die();
	}
}

include("../lib/periodes.inc.php");
$sql="SELECT e.login, e.nom, e.prenom, e.mef_code, e.id_eleve FROM eleves e, j_eleves_classes jec WHERE e.login=jec.login AND jec.id_classe='$id_classe' AND jec.periode='".($nb_periode-1)."' ORDER BY e.nom, e.prenom;";
//echo "$sql<br />";
$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_ele)==0) {
	echo "<p style=color:red'>Aucun élève dans la classe de&nbsp;: ".get_nom_classe($id_classe)."</p>";
	require("../lib/footer.inc.php");
	die();
}

/*
echo "<pre>";
print_r($tab_orientation);
echo "</pre>";
*/

if(!mef_avec_proposition_orientation($id_classe)) {
	echo "<p style=color:red'>La classe de '<strong>".get_nom_classe($id_classe)."</strong>' n'est pas associée à des MEFs d'un niveau d'orientation.</p>";
	require("../lib/footer.inc.php");
	die();
}

echo "<div style='float:right;width:3em;'><a href='#PDF'>PDF</a></div>";

$tab_orientation=get_tab_orientations_types_par_mef();
$tab_orientation2=get_tab_orientations_types();

$tab_orientation_classe_courante=get_tab_voeux_orientations_classe($id_classe);

$OrientationNbMaxVoeux=getSettingValue('OrientationNbMaxVoeux');
$OrientationNbMaxOrientation=getSettingValue('OrientationNbMaxOrientation');

echo "
<table class='boireaus boireaus_alt' summary='Consultation des voeux et orientation proposée'>
	<thead>
		<tr>
			<th>Élève</th>
			<th>Nom prénom</th>
			<th>Voeux</th>
			<th>Orientation proposée</th>
		</tr>
	</thead>
	<tbody>";

while($lig_ele=mysqli_fetch_object($res_ele)) {

	$chaine_voeux_orientation="";
	$chaine_orientation_proposee="";

	if(isset($tab_orientation_classe_courante['voeux'][$lig_ele->login])) {
		$chaine_voeux_orientation=get_liste_voeux_orientation($lig_ele->login);
	}

	if(isset($tab_orientation_classe_courante['orientation_proposee'][$lig_ele->login])) {
		$chaine_orientation_proposee=get_liste_orientations_proposees($lig_ele->login);
	}

	if(isset($tab_orientation_classe_courante['avis'][$lig_ele->login])) {
		$chaine_orientation_proposee.="<label for='avis_orientation_".$lig_ele->id_eleve."' style='vertical-align:top'><b title=\"Avis sur l'orientation proposée\">Avis&nbsp;</b> </label>".$tab_orientation_classe_courante['avis'][$lig_ele->login];
	}

	echo "
		<tr>
			<td><a href='../eleves/visu_eleve.php?ele_login=".$lig_ele->login."' target='_blank' title=\"Voir le classeur/dossier élève dans un nouvel onglet.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Onglets' /></a></td>
			<td>".$lig_ele->nom." ".$lig_ele->prenom."</td>
			<td style='text-align:left;'>".$chaine_voeux_orientation."</td>
			<td style='text-align:left;'>".$chaine_orientation_proposee."</td>
		</tr>";
}
echo "
	</tbody>
</table>

<a name='PDF'></a>
<p style='margin-top:1em;'>Exporter en PDF&nbsp;:</p>
<ul>
	<li><a href='export_pdf.php?id_classe=$id_classe&mode=voeux' target='_blank'>Voeux</a> seuls</li>
	<li><a href='export_pdf.php?id_classe=$id_classe&mode=orientations' target='_blank'>Orientations proposées</a> seules</li>
	<li><a href='export_pdf.php?id_classe=$id_classe&mode=voeux_et_orientations' target='_blank'>Voeux et orientations proposées</a></li>
</ul>
<p><a href='parametres_impression_pdf.php?id_classe=$id_classe' target='_blank'>Paramétrer le PDF</a></li>
<p><br /></p>\n";

require("../lib/footer.inc.php");
?>
