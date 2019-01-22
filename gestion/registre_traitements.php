<?php
/*
*
* Copyright 2001-2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Initialisation des feuilles de style après modification pour améliorer l'accessibilité
$accessibilite="y";

// On indique qu'il faut creer des variables non protégées (voir fonction cree_variables_non_protegees())
$variables_non_protegees = 'yes';

// Begin standart header
$niveau_arbo = 1;
$gepiPathJava="./..";

// Initialisations files
require_once("../lib/initialisations.inc.php");
include("../lib/initialisationsPropel.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

$sql="SELECT 1=1 FROM droits WHERE id='/gestion/registre_traitements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/gestion/registre_traitements.php',
administrateur='V',
professeur='V',
cpe='V',
scolarite='V',
eleve='V',
responsable='V',
secours='V',
autre='V',
description='Registre des traitements',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

// Check access
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('registre_traitements')) {
	header("Location: ../accueil.php?msg=Accès non ouvert");
	die();
}

//debug_var();

if($_SESSION['statut']=='administrateur') {
	if(isset($_POST['enregistrer'])) {
		check_token();

		$nb_reg=0;
		$msg='';

		$tab_param=array('RGPD_cnil_creation', 
				'RGPD_cnil_maj', 
				'RGPD_ref_cnil',
				'RGPD_delegue_protection_donnees',
				'RGPD_representant',
				'RGPD_resp_conjoint_1',
				'RGPD_resp_conjoint_2',
				'RGPD_resp_conjoint_3',
				'RGPD_finalite_statistique',
				'RGPD_mesures_securite',
				'RGPD_mesures_securite_techniques',
				'RGPD_mesures_securite_organisationnelles');
		foreach($tab_param as $param) {
			if(isset($_POST[$param])) {
				//echo "$param -&gt; y<br />";
				if(!saveSetting($param, 'y')) {
					$msg.="Erreur lors de l'enregistrement du paramètre '".$param."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
			else {
				//echo "$param -&gt; n<br />";
				if(!saveSetting($param, 'n')) {
					$msg.="Erreur lors de l'enregistrement du paramètre '".$param."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}

		$tab_param=array('RGPD_cnil_creation_date', 
				'RGPD_cnil_maj_date',
				'RGPD_maitrise_ouvrage_nom',
				'RGPD_maitrise_ouvrage_adresse',
				'RGPD_maitrise_ouvrage_tel',
				'RGPD_delegue_protection_donnees_nom',
				'RGPD_delegue_protection_donnees_adresse',
				'RGPD_delegue_protection_donnees_tel',
				'RGPD_representant_nom',
				'RGPD_representant_adresse',
				'RGPD_representant_tel',
				'RGPD_resp_conjoint_1_nom',
				'RGPD_resp_conjoint_1_adresse',
				'RGPD_resp_conjoint_1_tel',
				'RGPD_resp_conjoint_2_nom',
				'RGPD_resp_conjoint_2_adresse',
				'RGPD_resp_conjoint_2_tel',
				'RGPD_resp_conjoint_3_nom',
				'RGPD_resp_conjoint_3_adresse',
				'RGPD_resp_conjoint_3_tel',
				'RGPD_finalites',
				'RGPD_finalite_statistique_texte',
				'RGPD_mesures_securite_techniques_texte',
				'RGPD_mesures_securite_organisationnelles_texte',
				'RGPD_mesures_securite_explications');
		foreach($tab_param as $param) {
			if(isset($_POST[$param])) {
				if(!saveSetting($param, $_POST[$param])) {
					$msg.="Erreur lors de l'enregistrement du paramètre '".$param."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}

		$tab_param=array('RGPD_texte_presentation');
		foreach($tab_param as $param) {
			if(isset($NON_PROTECT[$param])) {
				$commentaire=nettoyage_retours_ligne_surnumeraires(traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$param])));
				if(!saveSetting($param, $commentaire)) {
					$msg.="Erreur lors de l'enregistrement du paramètre '".$param."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}

		$modules=array('RGPD_mod_absences', 
				'RGPD_mod_abs2', 
				'RGPD_mod_abs_prof', 
				'RGPD_mod_actions', 
				'RGPD_annees_anterieures', 
				'RGPD_mod_bulletins', 
				'RGPD_mod_CDT', 
				'RGPD_mod_CN', 
				'RGPD_mod_discipline', 
				'RGPD_mod_disc_pointage', 
				'RGPD_mod_alerte', 
				'RGPD_mod_edt_ical', 
				'RGPD_mod_EDT', 
				'RGPD_mod_engagements', 
				'RGPD_mod_EPB', 
				'RGPD_mod_EXB', 
				'RGPD_mod_RSS_CDT', 
				'RGPD_mod_genese_classes', 
				'RGPD_mod_gest_aid', 
				'RGPD_mod_inscriptions', 
				'RGPD_mod_ListesPerso', 
				'RGPD_mod_LSUN', 
				'RGPD_mod_OOO', 
				'RGPD_mod_notanet', 
				'RGPD_mod_orientation', 
				'RGPD_statuts_prives', 
				'RGPD_mod_trombinoscopes');
		foreach($modules as $current_module) {
			if(isset($NON_PROTECT[$current_module])) {
				$commentaire=nettoyage_retours_ligne_surnumeraires(traitement_magic_quotes(corriger_caracteres($NON_PROTECT[$current_module])));
				if(!saveSetting($current_module, $commentaire)) {
					$msg.="Erreur lors de l'enregistrement du commentaire sur le module '".$current_module."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}

		foreach($NON_PROTECT as $key => $commentaire) {
			if(preg_match('/^RGPD_plug_/', $key)) {
				$commentaire=nettoyage_retours_ligne_surnumeraires(traitement_magic_quotes(corriger_caracteres($commentaire)));
				if(!saveSetting($key, $commentaire)) {
					$msg.="Erreur lors de l'enregistrement du commentaire sur le plugin '".$key."'.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}

		if($nb_reg>0) {
			$msg.=$nb_reg." enregistrement(s) effectué(s).<br />";
		}

	}
}

include_once "../class_php/gestion/class_droit_acces_template.php";
$droitAffiche= new class_droit_acces_template();
include('droits_acces.inc.php');

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage = 'Des appréciations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Registre des traitements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

echo "<p class='bold'><a href=\"../accueil.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

if($_SESSION['statut']=='administrateur') {
	echo "<form action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='enregistrer' value='y' />";
}

echo "
<a name='presentation'></a>
<h2>Registre des traitements</h2>
<h3>Présentation</h3>

<div id='fixe2' class='fieldset_opacite50' style='padding:0.5em;'>
	<a href='#presentation'>Présentation</a><br />
	<a href='#generalites'>Généralités sur Gepi</a><br />
	<a href='#modules_actives'>Modules</a><br />
	<a href='#plugins'>Plugins</a><br />
	<a href='#droits_acces'>Droits d'accès</a><br />".(getSettingAOui('statuts_prives') ? "
	<a href='#droits_statuts_personnalises'>Droits statuts personnalisés</a><br />" :"")."
</div>

<p>La présente page est destinée à informer les utilisateurs sur les modules activés, les données traitées,...</p>";

$RGPD_texte_presentation=strtr(stripslashes(getSettingValue('RGPD_texte_presentation')), '"', "'");
if($_SESSION['statut']=='administrateur') {
	echo "
<p>Quelques liens pour comprendre la RGPD&nbsp;:</p>
<ul>
	<li><a href='https://www.cnil.fr/fr/comprendre-le-rgpd' target='_blank'>https://www.cnil.fr/fr/comprendre-le-rgpd</a></li>
	<li><a href='https://www.reseau-canope.fr/fileadmin/user_upload/Projets/RGPD/RGPD_WEB.pdf' target='_blank'>https://www.reseau-canope.fr/fileadmin/user_upload/Projets/RGPD/RGPD_WEB.pdf</a></li>
	<!--
	<li><a href='' target='_blank'></a></li>
	<li><a href='' target='_blank'></a></li>
	-->
</ul>
<p>Ces liens ne sont pas proposés par défaut dans Gepi pour les non-administrateurs.<br />
Libre à vous de les mentionner dans le petit texte facultatif de présentation qui suit&nbsp;:<br />
<textarea cols='60' name='no_anti_inject_RGPD_texte_presentation' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_texte_presentation."</textarea>
</p>";

}
elseif($RGPD_texte_presentation!='') {
	echo $RGPD_texte_presentation;
}

echo "

<pre style='color:red'>
A FAIRE apparaître :

Pouvoir éditer/modifier certaines des lignes qui suivent, en ajouter, supprimer selon les usages.

Vérifier les accès aux adresses parents dans des listes, exports,...
Droits aussi dans des AID... sur tel et mail élève,...

Pour les responsables légaux, avoir la liste des resp_legal=0 associés avec droits d'accès (bulletins, cahiers de textes,...) (acces_sp|envoi_bulletin)

</pre>";

$colspan='';
if($_SESSION['statut']=='administrateur') {
	$colspan=" colspan='2'";
}

$RGPD_ref_cnil=getSettingAOui('RGPD_ref_cnil');
$RGPD_cnil_creation=getSettingAOui('RGPD_cnil_creation');
$RGPD_cnil_maj=getSettingAOui('RGPD_cnil_maj');

echo "
<p class='bold'>Description du traitement</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Nom / sigle</th>
		<td".$colspan.">GEPI <em>(Gestion des élèves par internet)</em></td>
	</tr>
	<tr>
		<th>Établissement</th>
		<td".$colspan.">".getSettingValue('gepiSchoolName')."<br />
			".getSettingValue('gepiSchoolAdress1')."<br />
			".getSettingValue('gepiSchoolAdress2')."<br />
			".getSettingValue('gepiSchoolZipCode')." ".getSettingValue('gepiSchoolCity')."
		</td>
	</tr>";

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_ref_cnil'>
		<th>Réf CNIL</th>
		<td>".getSettingValue('num_enregistrement_cnil')."</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_ref_cnil' id='RGPD_ref_cnil' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('ref_cnil')\" ".($RGPD_ref_cnil ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_ref_cnil) {
	echo "
	<tr id='tr_ref_cnil'>
		<th>Réf CNIL</th>
		<td>".getSettingValue('num_enregistrement_cnil')."</td>
	</tr>";
}

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_cnil_creation'>
		<th>Date de création</th>
		<td><input type='text' name='RGPD_cnil_creation_date' value=\"".getSettingValue('RGPD_cnil_creation_date')."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_cnil_creation' id='RGPD_cnil_creation' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('cnil_creation')\" ".($RGPD_cnil_creation ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_cnil_creation) {
	echo "
	<tr id='tr_cnil_creation'>
		<th>Date de création</th>
		<td>".getSettingValue('RGPD_cnil_creation_date')."</td>
	</tr>";
}

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_cnil_maj'>
		<th>Mise à jour</th>
		<td><input type='text' name='RGPD_cnil_maj_date' value=\"".getSettingValue('RGPD_cnil_maj_date')."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_cnil_maj' id='RGPD_cnil_maj' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('cnil_maj')\" ".($RGPD_cnil_maj ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_cnil_creation) {
	echo "
	<tr id='tr_cnil_maj'>
		<th>Mise à jour</th>
		<td>".getSettingValue('RGPD_cnil_creation_date')."</td>
	</tr>";
}

echo "
</table>
<br />";

// =============================================================================
// Acteurs
$RGPD_maitrise_ouvrage_nom=getSettingValue('RGPD_maitrise_ouvrage_nom');
if($RGPD_maitrise_ouvrage_nom=='') {
	$RGPD_maitrise_ouvrage_nom=getSettingValue('gepiAdminPrenom')." ".getSettingValue('gepiAdminNom')." (".getSettingValue('gepiAdminFonction').")";
}
$RGPD_maitrise_ouvrage_adresse=getSettingValue('RGPD_maitrise_ouvrage_adresse');
if($RGPD_maitrise_ouvrage_adresse=='') {
	$RGPD_maitrise_ouvrage_adresse=getSettingValue('gepiSchoolAdress1').", ".getSettingValue('gepiSchoolAdress2').",".getSettingValue('gepiSchoolZipCode').",".getSettingValue('gepiSchoolCity').",".getSettingValue('gepiSchoolPays');
}
$RGPD_maitrise_ouvrage_tel=getSettingValue('RGPD_maitrise_ouvrage_tel');
if($RGPD_maitrise_ouvrage_tel=='') {
	$RGPD_maitrise_ouvrage_tel=getSettingValue('gepiSchoolTel');
}

$RGPD_delegue_protection_donnees=getSettingAOui('RGPD_delegue_protection_donnees');
$RGPD_delegue_protection_donnees_nom=getSettingValue('RGPD_delegue_protection_donnees_nom');
if($RGPD_delegue_protection_donnees_nom=='') {
	$RGPD_delegue_protection_donnees_nom=$RGPD_maitrise_ouvrage_nom;
}
$RGPD_delegue_protection_donnees_adresse=getSettingValue('RGPD_maitrise_ouvrage_adresse');
if($RGPD_delegue_protection_donnees_adresse=='') {
	$RGPD_delegue_protection_donnees_adresse=$RGPD_maitrise_ouvrage_adresse;
}
$RGPD_delegue_protection_donnees_tel=getSettingValue('RGPD_delegue_protection_donnees_tel');
if($RGPD_delegue_protection_donnees_tel=='') {
	$RGPD_delegue_protection_donnees_tel=$RGPD_maitrise_ouvrage_tel;
}

$RGPD_representant=getSettingAOui('RGPD_representant');
$RGPD_representant_nom=getSettingValue('RGPD_representant_nom');
$RGPD_representant_adresse=getSettingValue('RGPD_representant_adresse');
$RGPD_representant_tel=getSettingValue('RGPD_representant_tel');

$RGPD_resp_conjoint_1=getSettingAOui('RGPD_resp_conjoint_1');
$RGPD_resp_conjoint_1_nom=getSettingValue('RGPD_resp_conjoint_1_nom');
$RGPD_resp_conjoint_1_adresse=getSettingValue('RGPD_resp_conjoint_1_adresse');
$RGPD_resp_conjoint_1_tel=getSettingValue('RGPD_resp_conjoint_1_tel');

$RGPD_resp_conjoint_2=getSettingAOui('RGPD_resp_conjoint_2');
$RGPD_resp_conjoint_2_nom=getSettingValue('RGPD_resp_conjoint_2_nom');
$RGPD_resp_conjoint_2_adresse=getSettingValue('RGPD_resp_conjoint_2_adresse');
$RGPD_resp_conjoint_2_tel=getSettingValue('RGPD_resp_conjoint_2_tel');

$RGPD_resp_conjoint_3=getSettingAOui('RGPD_resp_conjoint_3');
$RGPD_resp_conjoint_3_nom=getSettingValue('RGPD_resp_conjoint_3_nom');
$RGPD_resp_conjoint_3_adresse=getSettingValue('RGPD_resp_conjoint_3_adresse');
$RGPD_resp_conjoint_3_tel=getSettingValue('RGPD_resp_conjoint_3_tel');

echo "
<p class='bold'>Acteurs</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Acteurs</th>
		<th>Nom</th>
		<th>Adresse</th>
		<th>Tél</th>";
if($_SESSION['statut']=='administrateur') {
	echo "
		<th></th>";
}
echo "
	</tr>";

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr>
		<th>Maîtrise d'ouvrage
		</th>
		<td><input type='text' name='RGPD_maitrise_ouvrage_nom' value=\"".$RGPD_maitrise_ouvrage_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_maitrise_ouvrage_adresse' value=\"".$RGPD_maitrise_ouvrage_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_maitrise_ouvrage_tel' value=\"".$RGPD_maitrise_ouvrage_tel."\" onchange=\"changement();\" /></td>
		<td><img src='../images/enabled.png' class='icone20' /></td>
	</tr>";
}
else {
	echo "
	<tr>
		<th>Maîtrise d'ouvrage
		</th>
		<td>".$RGPD_maitrise_ouvrage_nom."</td>
		<td>".$RGPD_maitrise_ouvrage_adresse."</td>
		<td>".$RGPD_maitrise_ouvrage_tel."</td>
	</tr>";
}

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_delegue_protection_donnees'>
		<th>Délégué à la protection des données</th>
		<td><input type='text' name='RGPD_delegue_protection_donnees_nom' value=\"".$RGPD_delegue_protection_donnees_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_delegue_protection_donnees_adresse' value=\"".$RGPD_delegue_protection_donnees_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_delegue_protection_donnees_tel' value=\"".$RGPD_delegue_protection_donnees_tel."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_delegue_protection_donnees' id='RGPD_delegue_protection_donnees' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('delegue_protection_donnees')\" ".($RGPD_delegue_protection_donnees ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_delegue_protection_donnees) {
	echo "
	<tr id='tr_delegue_protection_donnees'>
		<th>Délégué à la protection des données</th>
		<td>".$RGPD_delegue_protection_donnees_nom."</td>
		<td>".$RGPD_delegue_protection_donnees_adresse."</td>
		<td>".$RGPD_delegue_protection_donnees_tel."</td>
	</tr>";
}

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_representant'>
		<th>Représentant</th>
		<td><input type='text' name='RGPD_representant_nom' value=\"".$RGPD_representant_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_representant_adresse' value=\"".$RGPD_representant_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_representant_tel' value=\"".$RGPD_representant_tel."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_representant' id='RGPD_representant' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('representant')\" ".($RGPD_representant ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_representant) {
	echo "
	<tr id='tr_representant'>
		<th>Représentant</th>
		<td>".$RGPD_representant_nom."</td>
		<td>".$RGPD_representant_adresse."</td>
		<td>".$RGPD_representant_tel."</td>
	</tr>";
}

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_resp_conjoint_1'>
		<th>Responsable conjoint 1</th>
		<td><input type='text' name='RGPD_resp_conjoint_1_nom' value=\"".$RGPD_resp_conjoint_1_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_1_adresse' value=\"".$RGPD_resp_conjoint_1_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_1_tel' value=\"".$RGPD_resp_conjoint_1_tel."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_resp_conjoint_1' id='RGPD_resp_conjoint_1' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('resp_conjoint_1')\" ".($RGPD_resp_conjoint_1 ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_resp_conjoint_1) {
	echo "
	<tr id='tr_resp_conjoint_1'>
		<th>Responsable conjoint 1</th>
		<td>".$RGPD_resp_conjoint_1_nom."</td>
		<td>".$RGPD_resp_conjoint_1_adresse."</td>
		<td>".$RGPD_resp_conjoint_1_tel."</td>
	</tr>";
}


if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_resp_conjoint_2'>
		<th>Responsable conjoint 2</th>
		<td><input type='text' name='RGPD_resp_conjoint_2_nom' value=\"".$RGPD_resp_conjoint_2_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_2_adresse' value=\"".$RGPD_resp_conjoint_2_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_2_tel' value=\"".$RGPD_resp_conjoint_2_tel."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_resp_conjoint_2' id='RGPD_resp_conjoint_2' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('resp_conjoint_2')\" ".($RGPD_resp_conjoint_2 ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_resp_conjoint_2) {
	echo "
	<tr id='tr_resp_conjoint_2'>
		<th>Responsable conjoint 2</th>
		<td>".$RGPD_resp_conjoint_2_nom."</td>
		<td>".$RGPD_resp_conjoint_2_adresse."</td>
		<td>".$RGPD_resp_conjoint_2_tel."</td>
	</tr>";
}


if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_resp_conjoint_3'>
		<th>Responsable conjoint 3</th>
		<td><input type='text' name='RGPD_resp_conjoint_3_nom' value=\"".$RGPD_resp_conjoint_3_nom."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_3_adresse' value=\"".$RGPD_resp_conjoint_3_adresse."\" onchange=\"changement();\" /></td>
		<td><input type='text' name='RGPD_resp_conjoint_3_tel' value=\"".$RGPD_resp_conjoint_3_tel."\" onchange=\"changement();\" /></td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_resp_conjoint_3' id='RGPD_resp_conjoint_3' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('resp_conjoint_3')\" ".($RGPD_resp_conjoint_3 ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_resp_conjoint_3) {
	echo "
	<tr id='tr_resp_conjoint_3'>
		<th>Responsable conjoint 3</th>
		<td>".$RGPD_resp_conjoint_3_nom."</td>
		<td>".$RGPD_resp_conjoint_3_adresse."</td>
		<td>".$RGPD_resp_conjoint_3_tel."</td>
	</tr>";
}
echo "
</table>";

$RGPD_explication_acteurs=strtr(stripslashes(getSettingValue('RGPD_explication_acteurs')), '"', "'");
if($_SESSION['statut']=='administrateur') {
	echo "
<p>Vous pouvez ajouter un petit texte d'explication sur les rôles des acteurs mentionnés <em>(facultatif)</em>&nbsp;:<br />
<textarea cols='60' name='no_anti_inject_RGPD_explication_acteurs' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_explication_acteurs."</textarea>
</p>";
}
elseif($RGPD_explication_acteurs!='') {
	echo $RGPD_explication_acteurs;
}
// =============================================================================
// Finalités

$RGPD_finalites=strtr(stripslashes(getSettingValue('RGPD_finalites')), '"', "'");
if($RGPD_finalites=='') {
	$RGPD_finalites="Selon les modules activés (voir plus bas), cela peut concerner les notes, les bulletins, les cahiers de textes, les absences,...";
}

$RGPD_finalite_statistique=getSettingAOui('RGPD_finalite_statistique');
$RGPD_finalite_statistique_texte=strtr(stripslashes(getSettingValue('RGPD_finalite_statistique_texte')), '"', "'");
if($RGPD_finalite_statistique_texte=='') {
	$RGPD_finalite_statistique_texte="Gepi n'a pas de finalité statistique a priori, mais des exports statistiques sont possibles (statistiques anonymées de résultats scolaires, statistiques d'absences, statistiques d'incidents disciplinaires).";
}

echo "
<br />
<p class='bold'>Finalité(s) du traitement effectué</p>
<table class='boireaus boireaus_alt'>
	<tr>
		<th>Finalité</th>
		<td>
			Gestion de la scolarité des élèves.<br />";
if($_SESSION['statut']=='administrateur') {
	echo "
			<textarea cols='60' name='no_anti_inject_RGPD_finalites' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_finalites."</textarea>
		</td>
		<td>";
}
else {
	echo $RGPD_finalites;
}
echo "
		</td>
	</tr>";

if($_SESSION['statut']=='administrateur') {
	echo "
	<tr id='tr_finalite_statistique'>
		<th>Finalité statistique</th>
		<td>
			<textarea cols='60' name='no_anti_inject_RGPD_finalite_statistique_texte' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_finalite_statistique_texte."</textarea>
		</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_finalite_statistique' id='RGPD_finalite_statistique' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('finalite_statistique')\" ".($RGPD_finalite_statistique ? 'checked ' : '')."/>
		</td>
	</tr>";
}
elseif($RGPD_finalite_statistique) {
	echo "
	<tr id='tr_finalite_statistique'>
		<th>Finalité statistique</th>
		<td>
			$RGPD_finalite_statistique_texte
		</td>
	</tr>";
}
echo "
</table>
<br />";

// =============================================================================
// Mesures de sécurité

$RGPD_mesures_securite=getSettingAOui('RGPD_mesures_securite');

$RGPD_mesures_securite_techniques=getSettingAOui('RGPD_mesures_securite_techniques');
$RGPD_mesures_securite_techniques_texte=strtr(stripslashes(getSettingValue('RGPD_mesures_securite_techniques_texte')), '"', "'");

$RGPD_mesures_securite_organisationnelles=getSettingAOui('RGPD_mesures_securite_organisationnelles');
$RGPD_mesures_securite_organisationnelles_texte=strtr(stripslashes(getSettingValue('RGPD_mesures_securite_organisationnelles_texte')), '"', "'");

$RGPD_mesures_securite_explications=strtr(stripslashes(getSettingValue('RGPD_mesures_securite_explications')), '"', "'");

if($_SESSION['statut']=='administrateur') {
	echo "
<p class='bold'>Mesures de sécurité</p>
<p>Les mesures de sécurité recommandées sont détaillées dans les documents suivants&nbsp;:</p>
<ul>
	<li><a href='https://www.cnil.fr/fr/principes-cles/guide-de-la-securite-des-donnees-personnelles' target='_blank'>https://www.cnil.fr/fr/principes-cles/guide-de-la-securite-des-donnees-personnelles</a></li>
	<li><a href='https://www.ssi.gouv.fr/administration/reglementation/rgpd-renforcer-la-securite-des-donnees-a-caractere-personnel' target='_blank'>https://www.ssi.gouv.fr/administration/reglementation/rgpd-renforcer-la-securite-des-donnees-a-caractere-personnel</a></li>
</ul>
<p>L'hébergement de Gepi sur une machine qui n'est pas ouverte à tous les vents, dans un local fermé, sur une machine dédiée, avec des comptes protégés par mot de passe,... est souhaitable et peut être mentionné.</p>
<p>La sensibilisation des utilisateurs à la solidité des mots de passe est plus que recommandée pour les personnels, élèves et parents.<br />
Cela peut être mentionné,...</p>
<p>Ce préambule n'apparaît pas en compte administrateur pour vous permettre d'adapter ce que vous avez à dire.</p>
<p>Vous pouvez également ne pas faire apparaître du tout le paragraphe <strong>Mesures de sécurité</strong>, même si ce n'est pas recommandé&nbsp;:<br />
<input type='checkbox' name='RGPD_mesures_securite' id='RGPD_mesures_securite' value='y' onchange=\"changement(); checkbox_change(this.id)\" ".($RGPD_mesures_securite ? 'checked ' : '')."/><label for='RGPD_mesures_securite' id='texte_RGPD_mesures_securite'>Faire apparaître le paragraphe Mesures de sécurité pour les non-administrateurs.</label></p>

<table class='boireaus boireaus_alt'>
	<tr id='tr_mesures_securite_techniques'>
		<th>Mesures de sécurité techniques</th>
		<td>
			<textarea cols='60' name='no_anti_inject_RGPD_mesures_securite_techniques_texte' onchange=\"changement();\">".$RGPD_mesures_securite_techniques_texte."</textarea>
		</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_mesures_securite_techniques' id='RGPD_mesures_securite_techniques' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('mesures_securite_techniques')\" ".($RGPD_mesures_securite_techniques ? 'checked ' : '')."/>
		</td>
	</tr>
	<tr id='tr_mesures_securite_organisationnelles'>
		<th>Mesures de sécurité organisationnelles</th>
		<td>
			<textarea cols='60' name='no_anti_inject_RGPD_mesures_securite_organisationnelles_texte' onchange=\"changement();\">".$RGPD_mesures_securite_organisationnelles_texte."</textarea>
		</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_mesures_securite_organisationnelles' id='RGPD_mesures_securite_organisationnelles' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('mesures_securite_organisationnelles')\" ".($RGPD_mesures_securite_organisationnelles ? 'checked ' : '')."/>
		</td>
	</tr>
</table>

<p>Vous pouvez ajouter un petit texte d'explication supplémentaire <em>(facultatif)</em>&nbsp;:<br />
<textarea cols='60' name='no_anti_inject_RGPD_mesures_securite_explications' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mesures_securite_explications."</textarea>
</p>
<p>Si il n'y a ni ligne cochée dans le tableau, ni texte d'explication supplémentaire, la rubrique sécurité n'apparait pas pour les non-administrateurs.</p>
<br />";
}
elseif(($RGPD_mesures_securite)&&
(($RGPD_mesures_securite_techniques)||($RGPD_mesures_securite_organisationnelles)||($RGPD_mesures_securite_explications!=''))) {
	echo "<p class='bold'>Mesures de sécurité</p>";
	if($RGPD_mesures_securite_techniques||$RGPD_mesures_securite_organisationnelles) {
		echo "
<table class='boireaus boireaus_alt'>".($RGPD_mesures_securite_techniques ? "
	<tr id='tr_mesures_securite_techniques'>
		<th>Mesures de sécurité techniques</th>
		<td>
			<textarea cols='60' name='no_anti_inject_RGPD_mesures_securite_techniques_texte' onchange=\"changement();\">".$RGPD_mesures_securite_techniques_texte."</textarea>
		</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_mesures_securite_techniques' id='RGPD_mesures_securite_techniques' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('mesures_securite_techniques')\" ".($RGPD_mesures_securite_techniques ? 'checked ' : '')."/>
		</td>
	</tr>" : "").($RGPD_mesures_securite_organisationnelles ? "
	<tr id='tr_mesures_securite_organisationnelles'>
		<th>Mesures de sécurité organisationnelles</th>
		<td>
			<textarea cols='60' name='no_anti_inject_RGPD_mesures_securite_organisationnelles_texte' onchange=\"changement();\">".$RGPD_mesures_securite_organisationnelles_texte."</textarea>
		</td>
		<td title=\"Faire apparaître cette ligne pour les non-administrateurs.\">
			<input type='checkbox' name='RGPD_mesures_securite_organisationnelles' id='RGPD_mesures_securite_organisationnelles' value='y' onchange=\"changement(); RGPD_griser_lignes_a_masquer('mesures_securite_organisationnelles')\" ".($RGPD_mesures_securite_organisationnelles ? 'checked ' : '')."/>
		</td>
	</tr>" : "")."
</table>";
	}

	if($RGPD_mesures_securite_explications!='') {
		echo $RGPD_mesures_securite_explications;
	}
}

echo "
<script type='text/javascript'>
	function RGPD_griser_lignes_a_masquer(suffixe_id) {
		if(document.getElementById('RGPD_'+suffixe_id)) {
			if(document.getElementById('RGPD_'+suffixe_id).checked==true) {
				document.getElementById('tr_'+suffixe_id).style.backgroundColor='';
			}
			else {
				document.getElementById('tr_'+suffixe_id).style.backgroundColor='grey';
			}
		}
	}

	RGPD_griser_lignes_a_masquer('ref_cnil');
	RGPD_griser_lignes_a_masquer('cnil_creation');
	RGPD_griser_lignes_a_masquer('cnil_maj');
	//RGPD_griser_lignes_a_masquer('maitrise_ouvrage');
	RGPD_griser_lignes_a_masquer('delegue_protection_donnees');
	RGPD_griser_lignes_a_masquer('representant');
	RGPD_griser_lignes_a_masquer('resp_conjoint_1');
	RGPD_griser_lignes_a_masquer('resp_conjoint_2');
	RGPD_griser_lignes_a_masquer('resp_conjoint_3');
	RGPD_griser_lignes_a_masquer('finalite_statistique');
	RGPD_griser_lignes_a_masquer('mesures_securite_techniques');
	RGPD_griser_lignes_a_masquer('mesures_securite_organisationnelles');
	/*
	RGPD_griser_lignes_a_masquer('');
	RGPD_griser_lignes_a_masquer('');
	RGPD_griser_lignes_a_masquer('');
	RGPD_griser_lignes_a_masquer('');
	*/

	".js_checkbox_change_style()."
	".js_change_style_all_checkbox()."
</script>";

// =============================================================================
// Généralités
echo "
<a name='generalites'></a>
<h3>Généralités sur Gepi</h3>
<p>Indépendamment des modules activés et des droits d'accès donnés, Gepi permet aux utilisateurs d'accéder à un certain nombre d'informations.<br />
<span style='color:red'>A détailler...</span><br />
Les comptes administrateurs, scolarité <em>(chef d'établissement, adjoint, secrétaire)</em>, cpe on accès à... <span style='color:red'>à détailler...</span><br />
Les professeurs ont accès à leurs listes d'élèves avec leurs nom, prénom, genre (M/F), date de naissance et classe.<br />
Il peuvent exporter ces listes en fichiers CSV et PDF.</p>

<pre style='color:red'>
- Les statuts, leur rôle et les droits associés (http://www.sylogix.org/projects/gepi/wiki/Gepi_admin).
- Imports XML, quelles infos stockées, qui y a accès...
Les fichiers XML exploités avec quelles infos de Siècle/Sconet, logiciel d'emploi du temps, STS.
- ...

</pre>

<p>Dans Gepi, chaque utilisateur a un compte qui a un statut (et un seul) parmi&nbsp;:</p>
<ul>
	<li>
		<strong>administrateur</strong>&nbsp;: Les comptes administrateurs ne servent que pour&nbsp;:<br />
		le paramétrage général (nom de l'établissement, adresse,...)<br />
		l'initialisation de l'année (création des utilisateurs, des classes, des élèves,...)<br />
		les sauvegardes/restaurations<br />
		les réglages des droits d'accès (les professeurs peuvent ou non voir telle ou telle chose, les CPEs peuvent ou non...)<br />
		définir les modules de Gepi à activer<br />
		etc.
	</li>
	<li>
		<strong>scolarité</strong>&nbsp;: Ces comptes permettent de&nbsp;:<br />
		Ouvrir/fermer des périodes en saisie pour telle ou telle classe<br />
		Paramétrer et imprimer les bulletins<br />
		Imprimer les relevés de notes<br />
		Saisir les avis des conseils de classe<br />
		Visualiser les graphiques<br />
		etc.
	</li>
	<li>
		<strong>CPE</strong>&nbsp;: Les comptes CPE se paramètrent en fonction des besoins&nbsp;:<br />
		Ils peuvent saisir les absences sur les bulletins (import possible depuis Sconet absences)<br />
		Ils peuvent gérer les absences dans l'établissement (suivi et mise en place d'un suivi)<br />
		Faire un suivi de la saisie des absences par les professeurs<br />
		etc.
	</li>
	<li>
		<strong>professeur</strong>&nbsp;: Ces comptes peuvent saisir selon ce qui a été paramétré par l'administrateur&nbsp;:<br />
		des devoirs (notes)<br />
		des appréciations (bulletin)<br />
		le cahier de textes<br />
		les absences<br />
		les fiches du brevet (collège)<br />
		etc.
	</li>
	<li>
		<strong>secours</strong>&nbsp;: Le statut secours peut&nbsp;:<br />
		Saisir/corriger les appréciations et moyennes sur les bulletins (par exemple pour dépanner un professeur malade au moment du remplissage des bulletins...)<br />
		Saisir les absences à la place d'un CPE indisponible<br />
	</li>
	<li>
		<strong>élève</strong>&nbsp;: Ces comptes, quand ils sont créés, permettent à leur titulaire de (suivant les réglages de l'admin)&nbsp;:<br />
		visualiser leur relevé de notes<br />
		visualiser leur cahier de textes<br />
		télécharger leur photo sur le trombinoscope<br />
		visualiser leur équipe pédagogique<br />
		etc.
	</li>
	<li>
		<strong>responsable</strong>&nbsp;: Ces comptes, quand ils sont créés, permettent à leur titulaire de (suivant les réglages de l'admin)&nbsp;:<br />
		visualiser le relevé de notes de leur enfant<br />
		visualiser le cahier de textes de leur enfant<br />
		visualiser l'équipe pédagogique de leur enfant<br />
		etc.
	</li>
	<li>
		<strong>autre</strong>&nbsp;: Ce statut, dit &quot;personnalisé&quot;, permet à l'admin de paramétrer plus finement les droits confiés à un (ou des) utilisateur(s).<br />
		Cette gestion particulière se fait à partir de la page de gestion des utilisateurs, onglet &quot;statuts personnalisés&quot;.<br />
		Cette fonctionnalité permet notamment de créer un statut &quot;Chef d'établissement&quot;, ou bien &quot;Inspecteur&quot;, ou encore &quot;C.O.P.&quot;, en fonction des besoins mais aussi des usages de l'établissement.
	</li>
</ul>

<p>C'est un peu résumé, mais voilà pour les grandes lignes.<br />
Le compte administrateur ne doit normalement être utilisé qu'en début d'année.<br />
Par la suite, en gestion courante, c'est plutôt les comptes scolarité qui ont les droits appropriés.</p>";

//no_anti_inject_

// =============================================================================
// Modules
$RGPD_mod_absences=strtr(stripslashes(getSettingValue('RGPD_mod_absences')), '"', "'");
$RGPD_mod_abs2=strtr(stripslashes(getSettingValue('RGPD_mod_abs2')), '"', "'");
$RGPD_mod_abs_prof=strtr(stripslashes(getSettingValue('RGPD_mod_abs_prof')), '"', "'");
$RGPD_mod_actions=strtr(stripslashes(getSettingValue('RGPD_mod_actions')), '"', "'");
$RGPD_annees_anterieures=strtr(stripslashes(getSettingValue('RGPD_annees_anterieures')), '"', "'");
$RGPD_mod_bulletins=strtr(stripslashes(getSettingValue('RGPD_mod_bulletins')), '"', "'");
$RGPD_mod_CDT=strtr(stripslashes(getSettingValue('RGPD_mod_CDT')), '"', "'");
$RGPD_mod_CN=strtr(stripslashes(getSettingValue('RGPD_mod_CN')), '"', "'");
$RGPD_mod_discipline=strtr(stripslashes(getSettingValue('RGPD_mod_discipline')), '"', "'");
$RGPD_mod_disc_pointage=strtr(stripslashes(getSettingValue('RGPD_mod_disc_pointage')), '"', "'");
$RGPD_mod_alerte=strtr(stripslashes(getSettingValue('RGPD_mod_alerte')), '"', "'");
$RGPD_mod_edt_ical=strtr(stripslashes(getSettingValue('RGPD_mod_edt_ical')), '"', "'");
$RGPD_mod_EDT=strtr(stripslashes(getSettingValue('RGPD_mod_EDT')), '"', "'");
$RGPD_mod_engagements=strtr(stripslashes(getSettingValue('RGPD_mod_engagements')), '"', "'");
$RGPD_mod_EPB=strtr(stripslashes(getSettingValue('RGPD_mod_EPB')), '"', "'");
$RGPD_mod_EXB=strtr(stripslashes(getSettingValue('RGPD_mod_EXB')), '"', "'");
$RGPD_mod_RSS_CDT=strtr(stripslashes(getSettingValue('RGPD_mod_RSS_CDT')), '"', "'");
$RGPD_mod_genese_classes=strtr(stripslashes(getSettingValue('RGPD_mod_genese_classes')), '"', "'");
$RGPD_mod_gest_aid=strtr(stripslashes(getSettingValue('RGPD_mod_gest_aid')), '"', "'");
$RGPD_mod_inscriptions=strtr(stripslashes(getSettingValue('RGPD_mod_inscriptions')), '"', "'");
$RGPD_mod_ListesPerso=strtr(stripslashes(getSettingValue('RGPD_mod_ListesPerso')), '"', "'");
$RGPD_mod_LSUN=strtr(stripslashes(getSettingValue('RGPD_mod_LSUN')), '"', "'");
$RGPD_mod_OOO=strtr(stripslashes(getSettingValue('RGPD_mod_OOO')), '"', "'");
$RGPD_mod_notanet=strtr(stripslashes(getSettingValue('RGPD_mod_notanet')), '"', "'");
$RGPD_mod_orientation=strtr(stripslashes(getSettingValue('RGPD_mod_orientation')), '"', "'");
$RGPD_statuts_prives=strtr(stripslashes(getSettingValue('RGPD_statuts_prives')), '"', "'");
$RGPD_mod_trombinoscopes=strtr(stripslashes(getSettingValue('RGPD_mod_trombinoscopes')), '"', "'");

echo "
<br />
<a name='modules_actives'></a>
<h3>Modules activés</h3>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th style='width:10em;'>Module</th>
		<th style='width:20em;'>Finalité</th>
		<th style='width:50em;'>Explications</th>
	</tr>";

if(getSettingValue('active_module_absence')=='y') {
	echo "
	<tr>
		<td>Absences</td>
		<td>Gestion des absences et retards des élèves</td>
		<td style='text-align:left'>
			Le module absences permet de saisir les absences et retards des élèves.

			".(getSettingAOui('active_absences_parents') ? "<br />Les parents ont accès aux signalements d'absences enregistrés.<br />Les absences non traitées par la Vie Scolaire n'apparaissent que 4h après leur déclaration pour permettre de traiter une éventuelle erreur de saisie ou un défaut d'information sur une modification dans une activité." : "");
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_absences' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_absences."</textarea>";
	}
	elseif($RGPD_mod_absences!='') {
		echo "<br />".nl2br($RGPD_mod_absences);
	}
echo "
		</td>
	</tr>";
}

if(getSettingValue('active_module_absence')=='2') {
	echo "
	<tr>
		<td>Absences 2</td>
		<td>Gestion des absences et retards des élèves</td>
		<td style='text-align:left'>
			Le module absences permet de saisir les absences et retards des élèves.<br />
			Les professeurs constatent l'absence en classe à un moment donné.<br />
			Les personnels de Vie Scolaire (CPE) traitent les absences <em>(pour les catégoriser absence, retard inter-cours, retard extérieur, passage à l'infirmerie,...)</em> et contactent le cas échéant les responsables <em>(parents, tuteurs,...)</em>.<br />
			Les personnels de Vie Scolaire ont donc accès aux adresses postales, téléphoniques et mail.<br />
			Ils peuvent effectuer des extractions CSV/ODT des absences pour par exemple discuter des absences de tel élève ou dans telle classe.<br />

			Des absences répétées, non justifiées <em>(non valides)</em>, peuvent amener les CPE à effectuer un signalement à l'Inspection académique.<br />
			Des extractions statistiques peuvent aussi être demandées par l'Éducation Nationale.<br />
			<span style='color:red'>Préciser les champs extraits dans ces exports statistiques.</span><br />

			".(getSettingAOui('active_absences_parents') ? "<br />Les parents ont accès aux signalements d'absences enregistrés.<br />Les absences non traitées par la Vie Scolaire n'apparaissent que 4h après leur déclaration pour permettre de traiter une éventuelle erreur de saisie ou un défaut d'information sur une modification dans une activité." : "");

	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_abs2' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_abs2."</textarea>";
	}
	elseif($RGPD_mod_abs2!='') {
		echo "<br />".nl2br($RGPD_mod_abs2);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_module_absence_professeur')) {
	echo "
	<tr>
		<td>Abs/remplacements profs</td>
		<td>Gérer les absences de courte durée et remplacements de professeurs</td>
		<td style='text-align:left'>
			Le module est destiné à saisir des absences de courte durée (journée de stage,...) de professeurs.<br />
			Les créneaux libérés peuvent alors être proposés aux professeurs pour effectuer un remplacement.;";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_abs_prof' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_abs_prof."</textarea>";
	}
	elseif($RGPD_mod_abs_prof!='') {
		echo "<br />".nl2br($RGPD_mod_abs_prof);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_actions')) { 
	echo "
	<tr>
		<td>Actions</td>
		<td>Gestion des inscriptions/présence élèves sur, par exemple, les sorties/actions UNSS.</td>
		<td style='text-align:left'>
			Ce module permet de gérer les inscriptions d'élèves sur des actions, de pointer leur présence.<br />
			Les familles peuvent contrôler que la présence de leur enfant a bien été pointée.<br />
			Si les adresses mail des familles sont dans la base Gepi, une confirmation de présence peut être envoyée par mail.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_actions' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_actions."</textarea>";
	}
	elseif($RGPD_mod_actions!='') {
		echo "<br />".nl2br($RGPD_mod_actions);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_annees_anterieures')) { 
	echo "
	<tr>
		<td>Années antérieures</td>
		<td>Conserver les bulletins simplifiés des années antérieures des élèves</td>
		<td style='text-align:left'>
			Le module permet de conserver les bulletins simplifiés des années passées.<br />
			L'accès en consultation aux bulletins simplifiés des années passées est géré par des droits définis dans <a href='#droits_acces'>droits d'accès</a>.
			Les données sont conservées le temps de la scolarité de l'élève dans l'établissement.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_annees_anterieures' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_annees_anterieures."</textarea>";
	}
	elseif($RGPD_annees_anterieures!='') {
		echo "<br />".nl2br($RGPD_annees_anterieures);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_bulletins')) { 
	echo "
	<tr>
		<td>Bulletins</td>
		<td>Gérer les bulletins scolaires</td>
		<td style='text-align:left'>
			Le module permet aux professeurs de saisir les notes et appréciations qui apparaitront sur les bulletins, d'afficher ces informations dans des tableaux récapitulatifs, des graphiques.<br />
			Il permet aux comptes scolarité et éventuellement <em>(selon les droits donnés)</em> aux cpe et aux comptes professeurs désignés ".getSettingValue('gepi_prof_suivi')." de saisir les avis du conseil de classe.<br />
			Si des mentions sont définies, il est possible d'en attribuer aux élèves pour un bulletin de fin de période.<br />
			Selon les droits définis, les élèves/responsables peuvent consulter leurs bulletins/les bulletins de leurs enfants, une fois l'accès ouvert <em>(manuellement, ou à une date donnée,... en fin de période)</em>.
			L'accès en consultation aux bulletins simplifiés, à l'impression des bulletins,... est géré par des droits définis dans <a href='#droits_acces'>droits d'accès</a> pour les différentes catégories d'utilisateurs.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_bulletins' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_bulletins."</textarea>";
	}
	elseif($RGPD_mod_bulletins!='') {
		echo "<br />".nl2br($RGPD_mod_bulletins);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_cahiers_texte')) { 
	echo "
	<tr>
		<td>Cahiers de textes</td>
		<td>Gérer les cahiers de textes</td>
		<td style='text-align:left'>
			Le module permet aux enseignants de saisir leurs cahiers de textes <em>(compte-rendus de séance, travaux à faire pour la séance suivante)</em>.<br />
			".(getSettingAOui('cahier_texte_acces_public') ? "L'accès au cahiers de textes est public <em>(".(getSettingValue('cahiers_texte_passwd_pub')=='' ? "sans mot de passe" : "protégé par un mot de passe").")</em>.<br />
			Un lien en page de login permet de consulter tous les cahiers de textes de toutes les classes.<br />" : "")."
			Selon les droits donnés dans <a href='#droits_acces'>droits d'accès</a>, les différents statuts ont ou non accès aux cahiers de textes.<br />
			Lorsque le droit est donné, les élèves/parents n'ont accès qu'aux cahiers de textes les concernant <em>(leurs classes et enseignements)</em>.<br />
			Ils peuvent aussi si le droit leur est donné pointer les travaux faits ou non du CDT.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_CDT' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_CDT."</textarea>";
	}
	elseif($RGPD_mod_CDT!='') {
		echo "<br />".nl2br($RGPD_mod_CDT);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_carnets_notes')) { 
	echo "
	<tr>
		<td>Carnets de notes</td>
		<td>Gérer les notes des élèves</td>
		<td style='text-align:left'>Le module permet aux professeurs de saisir des notes, avec commentaire ou non.<br />
		Les professeurs peuvent choisir si les commentaires sur une évaluation doivent être visibles ou non des responsables <em>(les professeurs sont alertés qu'ils ne doivent pas saisir de commentaire déplacé)</em>.<br />

		<span style='color:red'>A FAIRE : Permettre de générer un export commentaires inclus pour le cas où un responsable réclamerait le détail des saisies.<br />
		Les parents peuvent aussi réclamer un export du module Discipline et d'autres... revoir ces possibilités d'export</span><br />

		Les professeurs peuvent choisir à partir de quelle date les notes seront visibles, définir des coefficients,...<br />
		Il est possible de créer des boîtes ou sous-matières dans les carnets de notes.<br />

		Les comptes scolarité peuvent générer des relevés de notes.<br />
		Les autres comptes voient ces droits d'accès définis dans la rubrique <a href='#droits_acces'>droits d'accès</a>.<br />

		".(getSettingAOui('GepiAccesEvalCumulEleve') ? "Les professeurs peuvent aussi créer des évaluations cumulées.<br />
		Il s'agit d'évaluations successives dont le cumul des points donne une note sur le cumul des référentiels.<br />
		Les professeurs peuvent choisir si telle évaluation-cumul est visible ou non des élèves/responsables.<br />
		Dans le cas où l'évaluation n'est pas visible dans le module évaluations-cumul, le cumul obtenu sera visible une fois transféré dans le carnet de notes." : "");

	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_CN' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_CN."</textarea>";
	}
	elseif($RGPD_mod_CN!='') {
		echo "<br />".nl2br($RGPD_mod_CN);
	}

	echo "</td>
	</tr>";
}

if(getSettingAOui('active_mod_ects')) { 
	echo "
	<tr>
		<td>Crédits ECTS</td>
		<td></td>
		<td style='text-align:left'>";

	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_ECTS' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_ECTS."</textarea>";
	}
	elseif($RGPD_mod_ECTS!='') {
		echo "<br />".nl2br($RGPD_mod_ECTS);
	}

	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_discipline')) { 
	echo "
	<tr>
		<td>Discipline</td>
		<td>Gérer les incidents et sanctions</td>
		<td style='text-align:left'>
			Le module Discipline est destiné à gérer les incidents, sanctions, avertissements (mises en garde) dans l'établissement.<br />
			Les comptes CPE et Scolarité ont le droit de saisir et consulter les incidents, sanctions, avertissements (mises en garde) pour tous les élèves.<br />
			Les comptes professeurs peuvent avoir des droits plus restreints.<br />
			Voir la rubrique <a href='#droits_acces'>droits d'accès</a>.<br />
			Le module permet aux comptes scolarité/cpe d'effectuer des extractions statistiques par classe, globale,... et des exports tableur si le module OpenOffice.org est activé.<br />";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_discipline' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_discipline."</textarea>";
	}
	elseif($RGPD_mod_discipline!='') {
		echo "<br />".nl2br($RGPD_mod_discipline);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_disc_pointage')) { 
	echo "
	<tr>
		<td>Discipline/pointage</td>
		<td>Pointage des menus incidents disciplinaires.</td>
		<td style='text-align:left'>
			Ce module permet de pointer de menus incidents ou manquements (travail non fait, oublis de matériel, comportements gênants).<br />
			Il permet de définir des seuils d'alerte par mail ou message en page d'accueil à destination des différentes catégories d'utilisateurs.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_disc_pointage' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_disc_pointage."</textarea>";
	}
	elseif($RGPD_mod_disc_pointage!='') {
		echo "<br />".nl2br($RGPD_mod_disc_pointage);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_alerte')) { 
	echo "
	<tr>
		<td>Dispositif d'alerte</td>
		<td>Dispositif d'alerte</td>
		<td style='text-align:left'>
			Ce module permet aux personnels de l'établissement disposant d'un compte de déposer des messages d'alerte à destination d'autres personnels choisis pour par exemple signaler à la Vie scolaire qu'un élève présent l'heure précédente n'a pas rejoint la salle suivante.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_alerte' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_alerte."</textarea>";
	}
	elseif($RGPD_mod_alerte!='') {
		echo "<br />".nl2br($RGPD_mod_alerte);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_edt_ical')) { 
	echo "
	<tr>
		<td>EDT Ical/Ics</td>
		<td>Emploi du temps importé depuis un fichier ICAL</td>
		<td style='text-align:left'>
			Le module permet de remplir les emplois de classes d'après un fichier ICAL provenant d'une autre application.<br />
			Les comptes scolarité et cpe ont accès aux emplois du temps de toutes les classes pour lesquelles un import a été effectué.<br />
			".(getSettingAOui('EdtIcalProf') ? "Les professeurs ont accès aux emplois du temps de leurs classes.<br />" : "")."
			".(getSettingAOui('EdtIcalProfTous') ? "Les professeurs ont accès aux emplois du temps de toutes les classes.<br />" : "")."
			".(getSettingAOui('EdtIcalEleve') ? "Les élèves ont accès aux emplois du temps de leurs classes.<br />" : "")."
			".(getSettingAOui('EdtIcalResponsable') ? "Les responsables ont accès aux emplois du temps des classes de leurs enfants.<br />" : "")."";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_edt_ical' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_edt_ical."</textarea>";
	}
	elseif($RGPD_mod_edt_ical!='') {
		echo "<br />".nl2br($RGPD_mod_edt_ical);
	}
	echo "
		</td>
	</tr>";
}

if((getSettingAOui('autorise_edt'))||(getSettingAOui('autorise_edt_eleve'))||(getSettingAOui('autorise_edt_admin'))) {
	echo "
	<tr>
		<td>Emplois du temps</td>
		<td>Permet de saisir/consulter les emplois du temps des classes, élèves, professeurs, salles.</td>
		<td style='text-align:left'>";
	if(getSettingAOui('autorise_edt_admin')) {
		echo "Les comptes administrateurs ont accès à la consultation, l'import, la saisie des emplois du temps dans Gepi, indépendamment de l'ouverture de l'accès aux emplois du temps aux autres utilisateurs de Gepi.<br />";
	}
	if(getSettingAOui('autorise_edt_tous')) {
		echo "Si les emplois du temps sont remplis, les professeurs, CPE et comptes scolarité on accès aux emplois du temps des classes, élèves, professeurs, salles dans Gepi.<br />
			Par défaut, les professeurs n'ont accès qu'à leur emploi du temps, pas à ce lui de leurs collègues.<br />
			Ce paramètre peut être modifié dans la rubrique <a href='#droits_acces'>droits d'accès</a>.";
			if(getSettingAOui('edt_remplir_prof')) {
				echo "<br />
			Les professeurs sont autorisés à remplir leur emploi du temps eux-mêmes <em>(ce n'est pas une autorisation à modifier son emploi du temps à sa convenance&nbsp;; l'Administration a simplement délègué la saisie dans Gepi)</em>.";
			}
	}
	if(getSettingAOui('autorise_edt_eleve2')) {
		echo "Les élèves et responsables ont accès à leurs emplois du temps et à celui de leur classe dans Gepi.";
	}
	elseif(getSettingAOui('autorise_edt_eleve')) {
		echo "Les élèves et responsables ont accès à leurs emplois du temps dans Gepi.";
	}

	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_EDT' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_EDT."</textarea>";
	}
	elseif($RGPD_mod_EDT!='') {
		echo "<br />".nl2br($RGPD_mod_EDT);
	}
	echo "</td>
	</tr>";
}

if(getSettingAOui('active_mod_engagements')) {
	echo "
	<tr>
		<td>Engagements</td>
		<td>Gérer les engagements des élèves/parents</td>
		<td style='text-align:left'>
			Ce module permet de définir des engagements, de les saisir, de les consulter.<br />
			Les engagements élèves sont en général Délégué de classe, suppléant, membre de l'association sportive...<br />
			Les engagements responsables/parents sont en général Représentant des élèves aux conseil de classe,...<br />
			Le module permet, dans d'autres modules, de cibler tels élèves/parents pour une communication, l'envoi de convocation au conseil de classe,...<br />
			Les engagements élèves, s'ils sont saisis dans Gepi, sont extraits et remontés vers les Livrets scolaire collège (LSU) et lycée (LSL) si les module/plugin correspondants sont activés dans Gepi.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_engagements' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_engagements."</textarea>";
	}
	elseif($RGPD_mod_engagements!='') {
		echo "<br />".nl2br($RGPD_mod_engagements);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_epreuve_blanche')) { 
	echo "
	<tr>
		<td>Epreuves blanches</td>
		<td>Gérer des épreuves blanches</td>
		<td style='text-align:left'>
			Le module permet de sélectionner des enseignements (les élèves inscrits dans ces enseignements), de choisir des professeurs correcteurs, de choisir les salles d'épreuve, d'y affecter les élèves, de générer les listes d'émargemement, liste d'affichage, les vignettes à coller sur les copies pour les anonymer, les vignettes à coller sur les tables, d'attribuer des copies aux professeurs (qui ne voient que le numéro d'anonymat lors de la saisie).<br />
			Les professeurs n'ont accès qu'à la saisie anonymée des copies qui leurs ont été attribuées.<br />
			Les comptes scolarité ou administrateurs peuvent générer les bilans et transférer les notes obtenues dans les carnets de notes des enseignements sélectionnés au départ.<br />";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_EPB' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_EPB."</textarea>";
	}
	elseif($RGPD_mod_EPB!='') {
		echo "<br />".nl2br($RGPD_mod_EPB);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_examen_blanc')) { 
	echo "
	<tr>
		<td>Examens blancs</td>
		<td>Gérer des examens blancs</td>
		<td style='text-align:left'>
			Le module permet aux comptes scolarité ou administrateurs de sélectionner des classes, des enseignements, de leur affecter des coefficients pour générer des bulletins d'examen par élève.<br />
			Dans chaque enseignement, le gestionnaire choisit une évaluation ou la moyenne d'une ou plusieurs périodes comme note à prendre en compte dans les bulletins.<br />
			Il est également possible de saisir des notes ne correspondant pas à des saisies déjà effectuées.<br />
			".(getSettingAOui('modExbPP') ? "Les comptes ".getSettingValue('gepi_prof_suivi')." sont autorisés à créer des examens blancs pour par exemple simuler des bilans d'examen d'après les résultats obtenus en cours d'année." : "")."";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_EXB' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_EXB."</textarea>";
	}
	elseif($RGPD_mod_EXB!='') {
		echo "<br />".nl2br($RGPD_mod_EXB);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('rss_cdt_eleve')) { 
	echo "
	<tr>
		<td>Flux RSS</td>
		<td>Générer un flux RSS du contenu du Cahier de textes</td>
		<td style='text-align:left'>
			Ce module est associé au module Cahier de textes.<br />
			Il permet aux élèves, sans qu'ils doivent se connecter avec leur compte utilisateur, d'accéder via une adresse (url) aux travaux à faire donnés dans les Cahiers de textes les concernant.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_RSS_CDT' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_RSS_CDT."</textarea>";
	}
	elseif($RGPD_mod_RSS_CDT!='') {
		echo "<br />".nl2br($RGPD_mod_RSS_CDT);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_genese_classes')) { 
	echo "
	<tr>
		<td>Genèse des classes</td>
		<td>Gérer les affectations des élèves dans des classes au changement d'année.</td>
		<td style='text-align:left'>
			Le module permet de choisir sur quelles classes futures on aura telles options.<br />
			Le module empêche l'inscription d'élèves avec des options non autorisées sur certaines classes.<br />
			Il permet de définir des profils d'élèves d'après leur niveau scolaire et leur attitude pour voir une fois les répartitions d'élèves effectuées si on arrive à une certaine hétérogénéité ou si on a une concentration d'élèves faibles ou difficiles dans une classe.<br />
			Le module est conçu pour qu'une fois les contraintes posées, un groupe de professeurs, personnels de Vie scolaire,... répartissent petit à petit les élèves dans les classes pour ne pas isoler des élèves et éviter des cocktails malheureux d'élèves.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_genese_classes' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_genese_classes."</textarea>";
	}
	elseif($RGPD_mod_genese_classes!='') {
		echo "<br />".nl2br($RGPD_mod_genese_classes);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_gest_aid')) { 
	echo "
	<tr>
		<td>Gestionnaires AID</td>
		<td>Étendre les possibilités sur les AID (Activités Inter-Disciplinaires)</td>
		<td style='text-align:left'>
			Le module permet de définir des gestionnaires d'AID pour effectuer les inscriptions d'élèves dans les AID à la place des comptes administrateurs.<br />
			Les gestionnaires peuvent être des comptes professeur, cpe ou scolarité.<br />
			Il est possible de générer des exports CSV".(getSettingAOui('active_module_trombinoscopes') ? ", de générer des trombinoscopes <em>(si le module Trombinoscopes est activé)</em>." : "")."<br />
			<span style='color:red'>à voir CSV droits pour exports DAREIC, Verdier,... accès aux mail, tel, adresses élèves/responsables</span><br />
			En collège, les AID sont utilisés pour gérer les EPI, AP et Parcours (Parcours avenir, Parcours santé,...) destinés à remonter vers le Livret Scolaire Collège (LSU).<br />";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_gest_aid' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_gest_aid."</textarea>";
	}
	elseif($RGPD_mod_gest_aid!='') {
		echo "<br />".nl2br($RGPD_mod_gest_aid);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_inscription')) { 
	echo "
	<tr>
		<td>Inscription</td>
		<td>Gérer des inscriptions de personnels sur des actions</td>
		<td style='text-align:left'>
			Le module Inscription vous permet de définir un ou plusieurs items (stage, intervention, ...), au(x)quel(s) les utilisateurs <em>(personnels de l'établissement)</em> pourront s'inscrire ou se désinscrire en cochant ou décochant une croix.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_inscriptions' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_inscriptions."</textarea>";
	}
	elseif($RGPD_mod_inscriptions!='') {
		echo "<br />".nl2br($RGPD_mod_inscriptions);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('GepiListePersonnelles')) { 
	echo "
	<tr>
		<td>Listes personnelles</td>
		<td>Génération de listes personnelles</td>
		<td style='text-align:left'>
			Le module permet aux enseignants de créer des listes personnelles d'élèves avec possibilité de saisir des commentaires.<br />
			Cela peut par exemple servir à un ".getSettingValue('gepi_prof_suivi')." pour pointer les documents transmis/récupérés.<br />
			Les professeurs ont accès dans ce module aux nom, prénom, genre (M/F) et classe des élèves choisis parmi leurs élèves <em>(Enseignements et AID)</em>.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_ListesPerso' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_ListesPerso."</textarea>";
	}
	elseif($RGPD_mod_ListesPerso!='') {
		echo "<br />".nl2br($RGPD_mod_ListesPerso);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_module_LSUN')) { 
	echo "
	<tr>
		<td>Livret Scolaire Unique</td>
		<td>Remontée des bulletins vers l'application nationale LSU</td>
		<td style='text-align:left'>
			Ce module permet de remonter vers l'application nationale LSU, les données saisies dans les bulletins des élèves.<br />
			L'application nationale LSU est destinée à conserver les bulletins au-delà de la scolarité de l'élève dans l'établissement <em>(et donc retrouver ses bulletins numériques après un changement d'établissement et même après la fin de sa scolarité au collège)</em>.<br />";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_LSUN' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_LSUN."</textarea>";
	}
	elseif($RGPD_mod_LSUN!='') {
		echo "<br />".nl2br($RGPD_mod_LSUN);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_ooo')) { 
	echo "
	<tr>
		<td>Modèles openDocument</td>
		<td>Génération/exportation de documents au format openDocument (OpenOffice.org, LibreOffice,...)</td>
		<td style='text-align:left'>
			Le module permet d'exporter des listes, de générer des documents Traitement de texte et Tableur au format openDocument (ODT/ODS) dans divers modules <em>(Discipline, Absences2,... s'ils sont activés)</em>.<br />
			Le module permet aussi le Publipostage pour générer des fichiers ODS/ODS d'après des listes d'élèves de classes, groupes,...<br />";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_OOO' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_OOO."</textarea>";
	}
	elseif($RGPD_mod_OOO!='') {
		echo "<br />".nl2br($RGPD_mod_OOO);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_notanet')) { 
	echo "
	<tr>
		<td>Notanet/Brevet</td>
		<td>Remontée des notes moyennes annuelles, appréciations et compétences du socle vers l'application nationale Notanet.</td>
		<td style='text-align:left'>
			Ce module n'est en principe plus utilisé depuis la mise en place de LSU <em>(voir module LSU)</em>.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_notanet' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_notanet."</textarea>";
	}
	elseif($RGPD_mod_notanet!='') {
		echo "<br />".nl2br($RGPD_mod_notanet);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_mod_orientation')) { 
	echo "
	<tr>
		<td>Orientation</td>
		<td>Orientation des élèves</td>
		<td style='text-align:left'>
			Ce module permet aux comptes scolarité de définir les orientations possibles et aux comptes scolarité et ".getSettingValue('gepi_prof_suivi')." de saisir les voeux d'orientation, de suggérer des orientations pour les faire apparaitre sur les bulletins.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_orientation' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_orientation."</textarea>";
	}
	elseif($RGPD_mod_orientation!='') {
		echo "<br />".nl2br($RGPD_mod_orientation);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('statuts_prives')) { 
	echo "
	<tr>
		<td>Statuts perso.</td>
		<td>Créer des statuts utilisateurs avec des droits particuliers</td>
		<td style='text-align:left'>

			Ces statuts, dit &quot;personnalisés&quot;, permettent à l'admin de paramétrer finement les droits confiés à un (ou des) utilisateur(s).<br />
			Cette gestion particulière se fait à partir de la page de gestion des utilisateurs, onglet &quot;statuts personnalisés&quot;.<br />
			Cette fonctionnalité permet notamment de créer un statut &quot;Chef d'établissement&quot;,<br />
			ou bien &quot;Inspecteur&quot;,<br />
			ou encore &quot;C.O.P.&quot;,<br />
			en fonction des besoins mais aussi des usages de l'établissement.<br />
			Il s'agit généralement de comptes avec des besoins limités, qui ne nécessitent pas un accès à toutes les fonctionnalités de Gepi et dont le foisonnement peut perdre un utilisateur qui n'en fait pas un usage très régulier.<br />

			<a href='#droits_statuts_personnalises'>Voir les droits pour les statuts personnalisés existants</a>.";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_statuts_prives' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_statuts_prives."</textarea>";
	}
	elseif($RGPD_statuts_prives!='') {
		echo "<br />".nl2br($RGPD_statuts_prives);
	}
	echo "
		</td>
	</tr>";
}

if(getSettingAOui('active_module_trombinoscopes')) { 
	echo "
	<tr>
		<td>Trombinoscopes</td>
		<td>Permet de consulter les photos des élèves d'une classe, d'un enseignement, d'une activité inter-disciplinaire (AID).</td>
		<td style='text-align:left'>
			Permet, selon les droits accordés dans <a href='#droits_acces'>droits d'accès</a>, aux utilisateurs de consulter les photos des élèves d'une classe, d'un enseignement, d'une activité inter-disciplinaire (AID).<br />
			Les comptes de tous les personnels de l'établissement ont accès en consultation aux photos des élèves de toutes les classes.<br />
			Les élèves ont accès à leur photo dans <a href='../utilisateurs/mon_compte.php'>Gérer mon compte</a>.<br />
			Les personnes autorisées à téléverser les photos des élèves sur le serveur Gepi sont par défaut les comptes 'administrateur' et 'scolarité'.<br />
			Le téléversement peut être ouvert aux CPE et ".getSettingValue('gepi_prof_suivi')." via des <a href='#droits_acces'>droits d'accès</a> supplémentaires.<br />
			".(getSettingAOui('GepiAccesEleTrombiPersonnels') ? "<br />
			Le trombinoscope des personnels de l'établissement est activé<br /><em>(cela ne signifie pas pour autant que la photo a nécessairement été téléversée sur le serveur Gepi)</em>." : "")."";
	if($_SESSION['statut']=='administrateur') {
		echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_mod_trombinoscopes' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$RGPD_mod_trombinoscopes."</textarea>";
	}
	elseif($RGPD_mod_trombinoscopes!='') {
		echo "<br />".nl2br($RGPD_mod_trombinoscopes);
	}
	echo "
		</td>
	</tr>";
}
echo "
</table>";

// =============================================================================
// Plugins:
echo "<br />
<a name='plugins'></a>
<h3>Plugins</h3>
<p>Des plugins peuvent être développés pour ajouter des fonctionnalités à Gepi.</p>";

include '../mod_plugins/traiterXml.class.php';
include '../mod_plugins/traiterRequetes.class.php';
//include '../mod_plugins/plugins.class.php';

# On liste les plugins
$liste_plugins  = array();
$open_dir       = scandir("../mod_plugins");

foreach ($open_dir as $dir) {
	// On vérifie la présence d'un point dans le nom retourné
	$test = explode(".", $dir);
	if (count($test) <= 1) {
		$test2 = PlugInPeer::getPluginByNom($dir);

		if (is_object($test2)) {
			$liste_plugins[] = $test2;
		}
		else {
			$liste_plugins[] = $dir;
		}
	}
}
/*
echo "<pre>";
print_r($liste_plugins);
echo "</pre>";
*/

echo "
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Plugin</th>
		<th>Description</th>
		<th>Auteur</th>
		<th>Description détaillée</th>
	</tr>";
foreach ($liste_plugins as $plugin) {
	if (is_object($plugin)){
		// le plugin est installé

		$xml = simplexml_load_file("../mod_plugins/".$plugin->getNom() . "/plugin.xml");
		$versiongepi=$xml->versiongepi;
		// On teste s'il est ouvert
		if ($plugin->getOuvert() == 'y') {
			echo "
	<tr>
		<td>".str_replace("_", " ", $plugin->getNom())."</td>
		<td>".$xml->description."</td>
		<td>".$xml->auteur."</td>
		<td style='text-align:left'>
		".(isset($xml->description_detaillee) ? nl2br($xml->description_detaillee) : "-");
		$commentaire_plug=strtr(stripslashes(getSettingValue('RGPD_plug_'.str_replace(" ", "_", $plugin->getNom()))), '"', "'");
		if($_SESSION['statut']=='administrateur') {
			// Permettre la modification du commentaire
			echo "<br /><textarea cols='60' name='no_anti_inject_RGPD_plug_".str_replace(" ", "_", $plugin->getNom())."' title=\"Commentaire supplémentaire à faire apparaître (facultatif).\" onchange=\"changement();\">".$commentaire_plug."</textarea>";
		}
		elseif($commentaire_plug!='') {
			echo "<br />".nl2br($commentaire_plug);
		}
		echo "
		</td>
	</tr>";
		}
	}
}
echo "
</table>";

if($_SESSION['statut']=='administrateur') {
	echo "<p style='margin-top:1em; margin-bottom:1em;'><em>NOTE pour les développeurs de plugins&nbsp;:</em> Il faut ajouter un champ 'description_detaillee' aux fichiers plugin.xml des plugins existants sur <a href='http://www.sylogix.org/projects/gepi/files' target='_blank'>http://www.sylogix.org/projects/gepi/files</a> pour expliquer les fonctionnalités, qui a le droit de faire quoi,...<br />
A défaut, il reste possible de préciser l'usage du plugin dans les champs proposés ci-dessus.</p>";
}
//==============================================================================
// Droits
echo "
<br />
<a name='droits_acces'></a>
<h3>Droits d'accès</h3>
<p>Des droits d'accès permettent de personnaliser des autorisations dans divers modules de Gepi&nbsp;:</p>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Rubrique ou module</th>
		<th>Public</th>
		<th>Droit</th>
	</tr>";
$rubrique_precedente='';
foreach($tab_droits_acces as $statutItem => $current_statut_item) {
	foreach($current_statut_item as $titreItem => $current_item) {
		if((in_array($_SESSION['statut'], $current_item['visibilite']))&&(getSettingAOui($titreItem))) {
			echo "
	<tr>
		<td style='text-align:left'>".$current_item['rubrique']."</td>
		<td>".ucfirst($statutItem)."</td>
		<td style='text-align:left'>".$current_item['texteItem']."</td>
	</tr>";
		}
	}
}
echo "
</table>";

//==============================================================================
// Droits statuts personnalisés
if(getSettingAOui('statuts_prives')) {
	include_once("../utilisateurs/creer_statut_autorisation.php");

	echo "
<br />
<a name='droits_statuts_personnalises'></a>
<h3>Statuts personnalisés</h3>";

	// Problème: Actuellement, on enregistre les url des pages accessibles, pas ce à quoi elles correspondent.
	// Ajouter une colonne à $menu_accueil pour préciser concrètement les droits donnés
	// Il va y avoir une colonne de plus dans droits_speciaux
	// Et faire un SELECT DISTINCT commentaire associé FROM droits_speciaux WHERE id_statut='...' AND autorisation='V';

	// Modifier les tableau $autorise et $menu_accueil en $autorise_statuts_personnalise et $menu_accueil_statuts_personnalise pour ne pas avoir de pb avec les include.

	$sql="SELECT du.login_user, ds.* FROM droits_utilisateurs du, 
							droits_statut ds 
						WHERE du.id_statut=ds.id;";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)==0) {
		echo "<p>Il n'existe actuellement aucun compte associé à un statut personnalisé.</p>";
	}
	else {
		echo "
<p>Voici la liste des statuts personnalisés et des droits qui leur sont donnés&nbsp;:</p>
<table class='boireaus boireaus_alt resizable sortable'>
	<tr>
		<th>Statut</th>
		<th>Droit</th>
	</tr>";
		while($lig=mysqli_fetch_object($res)) {
			$sql="SELECT DISTINCT commentaire FROM droits_speciaux WHERE id_statut='".$lig->id."' AND autorisation='V' ORDER BY commentaire;";
			//echo "$sql<br />";
			$res2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res2)>0) {
				while($lig2=mysqli_fetch_object($res2)) {
					echo "
	<tr>
		<td>".$lig->nom_statut."</td>
		<td style='text-align:left'>".$lig2->commentaire."</td>
	</tr>";
				}
			}
		}
		echo "
</table>";
	}
}

if($_SESSION['statut']=='administrateur') {
	echo "
		<div id='fixe'>
			<input type='submit' value='Enregistrer' />
		</div>
		<p><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";
}

require("../lib/footer.inc.php");
?>
