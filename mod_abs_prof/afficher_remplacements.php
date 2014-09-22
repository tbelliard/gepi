<?php
/*
 * $Id$
 *
 * Copyright 2001, 2014 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/afficher_remplacements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/afficher_remplacements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Afficher les remplacements de professeurs',
statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_mod_abs_prof')) {
	header("Location: ../accueil.php?msg=Module désactivé");
	die();
}

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");


if($mode=="") {
	// Futurs remplacements
	$tab_r=get_tab_propositions_remplacements("", "futures_validees");
	$liens_alt="<a href='".$_SERVER['PHP_SELF']."?mode=familles_informees' title=\"Voir les remplacements à venir pour lesquels les familles sont informées.\">Familles informées</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=familles_non_informees' title=\"Voir les remplacements à venir pour lesquels les familles ne sont pas informées.\">Familles non informées</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=tous' title=\"Voir tous les remplacements validés (passés et futurs).\">Tous</a>";
	$titre_h2="Remplacements (<em>validés</em>) à venir";
}
elseif($mode=="familles_informees") {
	// Futurs remplacements avec familles informées
	$tab_r=get_tab_propositions_remplacements("", "futures_validees", "oui");
	$liens_alt="<a href='".$_SERVER['PHP_SELF']."' title=\"Voir tous les remplacements à venir.\">Futurs</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=familles_non_informees' title=\"Voir les remplacements à venir pour lesquels les familles ne sont pas informées.\">Familles non informées</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=tous' title=\"Voir tous les remplacements validés (passés et futurs).\">Tous</a>";
	$titre_h2="Remplacements (<em>validés</em>) à venir avec familles informées";
}
elseif($mode=="familles_non_informees") {
	// Futurs remplacements avec familles informées
	$tab_r=get_tab_propositions_remplacements("", "futures_validees", "non");
	$liens_alt="<a href='".$_SERVER['PHP_SELF']."' title=\"Voir tous les remplacements à venir.\">Futurs</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=familles_informees' title=\"Voir les remplacements à venir pour lesquels les familles sont informées.\">Familles informées</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=tous' title=\"Voir tous les remplacements validés (passés et futurs).\">Tous</a>";
	$titre_h2="Remplacements (<em>validés</em>) à venir, familles non informées";
}
elseif($mode=="tous") {
	// Tous les remplacements passés et futurs
	$tab_r=get_tab_propositions_remplacements("", "validees", "");
	$liens_alt="<a href='".$_SERVER['PHP_SELF']."' title=\"Voir tous les remplacements à venir.\">Futurs</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=familles_informees' title=\"Voir les remplacements à venir pour lesquels les familles sont informées.\">Familles informées</a>";
	$liens_alt.=" | <a href='".$_SERVER['PHP_SELF']."?mode=familles_non_informees' title=\"Voir les remplacements à venir pour lesquels les familles ne sont pas informées.\">Familles non informées</a>";
	$titre_h2="Tous les remplacements (<em>validés</em>) à venir et passés";
}

if((isset($_POST['is_posted']))) {
	check_token();

	$msg="";

	$info_famille=isset($_POST['info_famille']) ? $_POST['info_famille'] : array();
	$texte_famille=isset($_POST['texte_famille']) ? $_POST['texte_famille'] : "";

	/*
	echo "<pre>";
	print_r($info_famille);
	echo "</pre>";
	*/

	$nb_info=0;
	foreach($info_famille as $key => $value) {
		$sql="UPDATE abs_prof_remplacement SET info_famille='oui', texte_famille='$texte_famille' WHERE id='".$key."';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$update) {
			$msg.="Erreur lors de l'enregistrement de l'information des familles pour le remplacement n°$key.<br />";
		}
		else {
			$nb_info++;
		}
	}

	$nb_suppr=0;
	for($loop=0;$loop<count($tab_r);$loop++) {
		if(!array_key_exists($tab_r[$loop]['id'], $info_famille)) {
			$sql="SELECT 1=1 FROM abs_prof_remplacement WHERE info_famille='oui' AND id='".$tab_r[$loop]['id']."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$sql="UPDATE abs_prof_remplacement SET info_famille='' WHERE id='".$tab_r[$loop]['id']."';";
				$update=mysqli_query($GLOBALS["mysqli"], $sql);
				if(!$update) {
					$msg.="Erreur lors de l'enregistrement de la suppression d'information des familles pour le remplacement n°$key.<br />";
				}
				else {
					$nb_suppr++;
				}
			}
		}
	}

	if($nb_info>0) {
		$msg.="Information des familles enregistrée pour $nb_info remplacement(s).<br />";
	}

	if($nb_suppr>0) {
		$msg.="Information des familles supprimée pour $nb_suppr remplacement(s).<br />";
	}

	// Après modification, on récupère la nouvelle liste:
	if($mode=="") {
		// Futurs remplacements
		$tab_r=get_tab_propositions_remplacements("", "futures_validees");
	}
	elseif($mode=="familles_informees") {
		// Futurs remplacements avec familles informées
		$tab_r=get_tab_propositions_remplacements("", "futures_validees", "oui");
	}
	elseif($mode=="familles_non_informees") {
		// Futurs remplacements avec familles informées
		$tab_r=get_tab_propositions_remplacements("", "futures_validees", "non");
	}
	elseif($mode=="tous") {
		// Tous les remplacements passés et futurs
		$tab_r=get_tab_propositions_remplacements("", "validees", "");
	}

}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$avec_js_et_css_edt="y";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// onclick=\"return confirm_abandon (this, change, '$themessage')\"
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Afficher remplacements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************

//debug_var();

//===================================================================
// Récupérer la liste des créneaux
$tab_creneau=get_heures_debut_fin_creneaux();
//===================================================================

echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | $liens_alt
</p>";

//============================================================================================================
/*
if((getSettingAOui('autorise_edt_tous'))||
	((getSettingAOui('autorise_edt_admin'))&&($_SESSION['statut']=='administrateur'))) {
	// Lien vers l'EDT des salles
	echo "
<div style='float:right; width:5em; text-align:center;' class='fieldset_opacite50' title=\"Voir l'emploi du temps des salles dans une nouvelle page.\"><a href='../edt_organisation/index_edt.php?visioedt=salle1' target='_blank'>EDT des salles</a></div>";

	// Dispositif pour l'affichage EDT en infobulle

	$titre_infobulle="EDT de <span id='id_ligne_titre_infobulle_edt'></span>";
	$texte_infobulle="";
	$tabdiv_infobulle[]=creer_div_infobulle('edt_prof',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

	function affiche_lien_edt_prof($login_prof, $info_prof) {
		return " <a href='../edt_organisation/index_edt.php?login_edt=".$login_prof."&amp;type_edt_2=prof&amp;no_entete=y&amp;no_menu=y&amp;lien_refermer=y' onclick=\"affiche_edt_prof_en_infobulle('$login_prof', '".addslashes($info_prof)."');return false;\" title=\"Emploi du temps de ".$info_prof."\" target='_blank'><img src='../images/icons/edt.png' class='icone16' alt='EDT' /></a>";
	}

	$titre_infobulle="EDT de la classe de <span id='span_id_nom_classe'></span>";
	$texte_infobulle="";
	$tabdiv_infobulle[]=creer_div_infobulle('edt_classe',$titre_infobulle,"",$texte_infobulle,"",40,0,'y','y','n','n');

	echo "
<style type='text/css'>
	.lecorps {
		margin-left:0px;
	}
</style>

<script type='text/javascript'>
	function affiche_edt_classe_en_infobulle(id_classe, classe) {
		document.getElementById('span_id_nom_classe').innerHTML=classe;

		new Ajax.Updater($('edt_classe_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+id_classe+'&type_edt_2=classe&visioedt=classe1&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_classe','y',-20,20);
	}

	function affiche_edt_prof_en_infobulle(login_prof, info_prof) {
		document.getElementById('id_ligne_titre_infobulle_edt').innerHTML=info_prof;

		new Ajax.Updater($('edt_prof_contenu_corps'),'../edt_organisation/index_edt.php?login_edt='+login_prof+'&type_edt_2=prof&no_entete=y&no_menu=y&mode_infobulle=y',{method: 'get'});
		afficher_div('edt_prof','y',-20,20);
	}
</script>\n";
}
else {
	function affiche_lien_edt_prof($login_prof, $info_prof) {
		return "";
	}
}
//============================================================================================================
*/

	echo "
<h2>$titre_h2</h2>

<p class='bold'>Liste des remplacements validés&nbsp;:</p>";

	if(count($tab_r)==0) {
		echo "<p>Aucun remplacement n'est validé.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	echo "
<form action=\"".$_SERVER['PHP_SELF']."#debut_de_page\" method=\"post\" style=\"width: 100%;\" name=\"formulaire_saisie_login_user\">
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='is_posted' value='y' />
		<input type='hidden' name='mode' value='$mode' />

		<table class='boireaus boireaus_alt sortable resizable'>
			<thead>
				<tr>
					<th class='text' title=\"Cliquez pour trier\">Date</th>
					<th class='text' title=\"Cliquez pour trier\">Classe</th>
					<th class='text' title=\"Cliquez pour trier\">Professeur remplaçant</th>
					<th class='text' title=\"Cliquez pour trier\">Commentaire prof</th>
					<th class='text' title=\"Cliquez pour trier\">Commentaire validation</th>
					<th class='text' title=\"Cliquez pour trier\">Salle</th>
					<th class='text' title=\"Cliquez pour trier\">Cours remplacé</th>
					<th class='nosort' title=\"Pas de tri sur cette colonne\">
						Familles informées<br />
						<a href='#' onclick=\"tout_cocher();return false;\" title=\"Cocher pour informer les familles pour tous les remplacements affichés.\"><img src='../images/enabled.png' class='icone20' alt='Cocher' /></a> / <a href='#' onclick=\"tout_decocher();return false;\" title=\"Tout décocher pour ne pas informer les familles.\"><img src='../images/disabled.png' class='icone20' alt='Décocher' /></a>
					</th>
				</tr>
			</thead>
			<tbody>";

	$civ_nom_prenom=array();
	$nom_classe=array();
	for($loop=0;$loop<count($tab_r);$loop++) {

		if(!isset($civ_nom_prenom[$tab_r[$loop]['login_user']])) {
				$civ_nom_prenom[$tab_r[$loop]['login_user']]=civ_nom_prenom($tab_r[$loop]['login_user']);
		}
		if(!isset($nom_classe[$tab_r[$loop]['id_classe']])) {
				$nom_classe[$tab_r[$loop]['id_classe']]=get_nom_classe($tab_r[$loop]['id_classe']);
		}

		echo "
				<tr>
					<td>
						<span style='display:none'>".$tab_r[$loop]['date_debut_r']."</span>
						".formate_date($tab_r[$loop]['date_debut_r'], "n", "complet")." de ".$tab_creneau[$tab_r[$loop]['id_creneau']]['debut_court']." à ".$tab_creneau[$tab_r[$loop]['id_creneau']]['fin_court']." (<em>".$tab_creneau[$tab_r[$loop]['id_creneau']]['nom_creneau']."</em>)
					</td>
					<td>".$nom_classe[$tab_r[$loop]['id_classe']]."</td>
					<td>".$civ_nom_prenom[$tab_r[$loop]['login_user']]."</td>
					<td>".$tab_r[$loop]['commentaire_prof']."</td>
					<td>".$tab_r[$loop]['commentaire_validation']."</td>
					<td>".$tab_r[$loop]['salle']."</td>
					<td style='font-size:small'>".get_info_grp($tab_r[$loop]['id_groupe'])."</td>
					<td>";
		if($tab_r[$loop]['info_famille']=="oui") {
			$checked=" checked";
		}
		else {
			$checked="";
		}
		echo "<input type='checkbox' name='info_famille[".$tab_r[$loop]['id']."]' id='info_famille_$loop' value='oui'$checked /></td>
				</tr>";
	}
	$nb_lignes=$loop;

	$abs_prof_modele_message_eleve=getSettingValue('abs_prof_modele_message_eleve');
	if($abs_prof_modele_message_eleve=="") {
		$abs_prof_modele_message_eleve="En raison de l'absence de __PROF_ABSENT__, le cours __COURS__ du __DATE_HEURE__ sera remplacé par un cours avec __PROF_REMPLACANT__ en salle __SALLE__.";
		saveSetting('abs_prof_modele_message_eleve', $abs_prof_modele_message_eleve);
	}

	$lien_edt_ical="";
	$commentaire_edt_ical="";
	if((getSettingAOui('active_edt_ical'))&&((getSettingAOui('EdtIcalEleve'))||(getSettingAOui('EdtIcalResponsable')))) {
		$lien_edt_ical="\n__LIEN_EDT_ICAL__";
		$commentaire_edt_ical="<br />La chaine __LIEN_EDT_ICAL__ sera remplacée par un lien vers l'Emploi du temps importé depuis un fichier ICAL/ICS.<br />Cela ne présente d'intérêt que si vous envoyez un fichier ICAL/ICS avec le remplacement pris en compte.";
	}

	echo "
			</tbody>
		</table>

		<p>Texte affiché aux élèves et parents dans le cas où les familles sont informées&nbsp;:</p>
		<textarea name='texte_famille' cols='60' rows='5'>En raison de l'absence de __PROF_ABSENT__, le cours __COURS__ du __DATE_HEURE__ sera remplacé par un cours avec __PROF_REMPLACANT__ en salle __SALLE__.$lien_edt_ical</textarea>

		<p><input type='submit' value='Valider' /></p>

		<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTES&nbsp;:</em> Les chaines __PROF_ABSENT__, __COURS__, __DATE_HEURE__, __PROF_REMPLACANT__ et __SALLE__ seront remplacées par les valeurs/textes appropriés.".$commentaire_edt_ical."</p>

	</fieldset>
</form>

<script type='text/javascript'>

	function tout_cocher() {
		for(i=0;i<$nb_lignes;i++) {
			if(document.getElementById('info_famille_'+i)) {
				document.getElementById('info_famille_'+i).checked=true;
			}
		}
	}

	function tout_decocher() {
		for(i=0;i<$nb_lignes;i++) {
			if(document.getElementById('info_famille_'+i)) {
				document.getElementById('info_famille_'+i).checked=false;
			}
		}
	}

</script>";

require("../lib/footer.inc.php");
?>
