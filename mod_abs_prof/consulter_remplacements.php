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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_abs_prof/consulter_remplacements.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_abs_prof/consulter_remplacements.php',
administrateur='V',
professeur='F',
cpe='V',
scolarite='V',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Consulter les remplacements de professeurs',
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

$annee = strftime("%Y");
$mois = strftime("%m");
$jour = strftime("%d");
if($mois>7) {
	$date_debut_tmp="01/09/$annee";
	// Et au format MySQL:
	$date_debut_annee="$annee-09-01";
	$date_du_jour="$annee-$mois-$jour";
}
else {
	$date_debut_tmp="01/09/".($annee-1);
	// Et au format MySQL:
	$date_debut_annee=($annee-1)."-09-01";
	$date_du_jour=($annee-1)."-$mois-$jour";
}
$date_debut_remplac=isset($_POST['date_debut_remplac']) ? $_POST['date_debut_remplac'] : (isset($_SESSION['date_debut_remplac']) ? $_SESSION['date_debut_remplac'] : $date_debut_tmp);
$date_fin_remplac=isset($_POST['date_fin_remplac']) ? $_POST['date_fin_remplac'] : (isset($_SESSION['date_fin_remplac']) ? $_SESSION['date_fin_remplac'] : "$jour/$mois/$annee");
$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;

$liens_alt="";
if($mode=="extraction") {
	$liens_alt=" | <a href='".$_SERVER['PHP_SELF']."' title=\"Effectuer une autre extraction.\">Autre extraction</a>";
}

/*
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

	echo "<pre>";
	print_r($info_famille);
	echo "</pre>";

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
*/

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$avec_js_et_css_edt="y";

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
// onclick=\"return confirm_abandon (this, change, '$themessage')\"
$message_suppression = "Confirmation de suppression";
//**************** EN-TETE *****************
$titre_page = "Consulter remplacements";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************
//debug_var();

//===================================================================
// Récupérer la liste des créneaux
$tab_creneau=get_heures_debut_fin_creneaux();
//===================================================================

echo "<a name=\"debut_de_page\"></a>
<p class='bold'>
	<a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>".$liens_alt."
</p>";

// Proposer d'extraire les remplacements entre telle et telle date, pour tous les profs ou pour une sélection de profs
// Afficher ensuite deux tableaux, l'un de totaux, l'autre du détail des remplacements avec date et heure.

if($mode=="") {
	echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' name='form1'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<h3>Extraction des remplacements validés</h3>

		<p>Intervalle de dates&nbsp;: du 
		<input type='text' name = 'date_debut_remplac' id='date_debut_remplac' size='10' value = \"".$date_debut_remplac."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_debut_remplac", "img_bouton_date_debut_remplac")." au <input type='text' name = 'date_fin_remplac' id='date_fin_remplac' size='10' value = \"".$date_fin_remplac."\" onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\" />".img_calendrier_js("date_fin_remplac", "img_bouton_date_fin_remplac")."</p>

		<p>Extraire les remplacements pour le ou les professeurs cochés ci-dessous&nbsp;:</p>
		".liste_checkbox_utilisateurs(array('professeur'), 
							array(), 
							'login_prof', 
							'cocher_decocher', 
							"n",
							"SELECT DISTINCT u.login, 
								u.civilite, 
								u.nom, 
								u.prenom, 
								u.statut 
							FROM utilisateurs u, 
								abs_prof_remplacement apr 
							WHERE u.statut='professeur' AND 
								u.etat='actif' AND 
								u.login=apr.login_user AND 
								apr.validation_remplacement='oui' 
							ORDER BY statut, nom, prenom, login;")."
		<p><a href='#' onclick='cocher_decocher(true);return false;'>Cocher</a>/<a href='#' onclick='cocher_decocher(false);return false;'>décocher</a> tous les professeurs</p>

		<p><input type='hidden' name='mode' value='extraction' />
		<p><input type='submit' value='Extraire' /></p>
	</fieldset>
<form>

<script type='text/javascript'>
	".js_checkbox_change_style('checkbox_change', 'texte_', 'n')."
</script>

<p style='color:red'><em>A FAIRE&nbsp;:</em></p>
<ul>
	<li>Liste des cours non remplacés et cours remplacés par jour.<br />
	Avec indication de l'effectif susceptible de se retrouver en permanence.</li>
	<li>Liste des remplacements effectués, pour une éventuelle(?) rémunaration.</li>
</ul>";
}
elseif($mode=='extraction') {
	//===================================================================
	// Récupérer la liste des créneaux
	$tab_creneau=get_heures_debut_fin_creneaux();
	//===================================================================

	echo "
	<h3>Extraction des remplacements validés</h3>
	<p>Remplacements effectués entre le ".$date_debut_remplac." et le ".$date_fin_remplac." pour le ou les professeurs choisis&nbsp;:</p>
	<table class='boireaus boireaus_alt resizable sortable'>
		<tr>
			<th class='text' title=\"Cliquez pour trier\">Date</th>
			<th class='text' title=\"Cliquez pour trier\">Classe</th>
			<th class='text' title=\"Cliquez pour trier\">Professeur remplaçant</th>
			<th class='text' title=\"Cliquez pour trier\">Commentaire prof</th>
			<th class='text' title=\"Cliquez pour trier\">Commentaire validation</th>
			<th class='text' title=\"Cliquez pour trier\">Salle</th>
			<th class='text' title=\"Cliquez pour trier\">Cours remplacé</th>
		</tr>";

	$mysql_date_debut_remplac=get_mysql_date_from_slash_date($date_debut_remplac);
	$mysql_date_fin_remplac=get_mysql_date_from_slash_date($date_fin_remplac);

	$nom_classe=array();
	$civ_nom_prenom=array();
	$info_grp=array();

	for($loop=0;$loop<count($login_prof);$loop++) {
		$sql="SELECT * FROM abs_prof_remplacement WHERE validation_remplacement='oui' AND date_debut_r>='$mysql_date_debut_remplac' AND date_fin_r<='$mysql_date_fin_remplac' AND login_user='".$login_prof[$loop]."' ORDER BY date_debut_r;";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			while($lig=mysqli_fetch_object($res)) {
				if(!isset($nom_classe[$lig->id_classe])) {
					$nom_classe[$lig->id_classe]=get_nom_classe($lig->id_classe);
				}
				if(!isset($civ_nom_prenom[$lig->login_user])) {
					$civ_nom_prenom[$lig->login_user]=civ_nom_prenom($lig->login_user);
				}
				if(!isset($info_grp[$lig->id_groupe])) {
					$info_grp[$lig->id_groupe]=get_info_grp($lig->id_groupe);
				}
				echo "
		<tr>
			<td>
				<span style='display:none'>".$lig->date_debut_r."</span>
				".formate_date($lig->date_debut_r, "n", "complet")." de ".$tab_creneau[$lig->id_creneau]['debut_court']." à ".$tab_creneau[$lig->id_creneau]['fin_court']." (<em>".$tab_creneau[$lig->id_creneau]['nom_creneau']."</em>)
			</td>
			<td>".$nom_classe[$lig->id_classe]."</td>
			<td>".$civ_nom_prenom[$lig->login_user]."</td>
			<td>".$lig->commentaire_prof."</td>
			<td>".$lig->commentaire_validation."</td>
			<td>".$lig->salle."</td>
			<td style='font-size:small'>".$info_grp[$lig->id_groupe]."</td>
		</tr>";
			}
		}
	}

	echo "
	</table>";
}
else {
	echo "<p style='color:red'>Mode non implémenté.</p>";
}
require("../lib/footer.inc.php");
?>
