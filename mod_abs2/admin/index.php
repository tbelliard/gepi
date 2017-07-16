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
require_once("../../lib/initialisations.inc.php");
//mes fonctions
//include("../lib/functions.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// Check access
if (!checkAccess()) {
    header("Location: ../../logout.php?auto=1");
    die();
}

$retour=$_SESSION['retour'];
$_SESSION['retour']=$_SERVER['PHP_SELF'] ;

$msg = '';

// On laisse seulement un administrateur modifier les paramétrages dans cette page:
if (($_SESSION['statut']=="administrateur")&&(isset($_POST['is_posted']))) {
	if ($_POST['is_posted']=='1') {

		if (isset($_POST['activer'])) {
			if (!saveSetting("active_module_absence", $_POST['activer'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation !";
			}
		}
		if (isset($_POST['activer_prof'])) {
			if (!saveSetting("active_module_absence_professeur", $_POST['activer_prof'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie par les professeurs !";
			}
		}
		if (isset($_POST['activer_resp'])) {
			if (!saveSetting("active_absences_parents", $_POST['activer_resp'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la consultation par les responsables élèves !";
			}
		}
		if (isset($_POST['gepiAbsenceEmail'])) {
			if (!saveSetting("gepiAbsenceEmail", $_POST['gepiAbsenceEmail'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre gestion 'absence email' !";
			}
		}

		if (isset($_POST['abs2_afficher_alerte_nj'])) {
			if (!saveSetting("abs2_afficher_alerte_nj", "y")) {
				$msg = "Erreur lors de l'enregistrement à 'y' du paramètre 'abs2_afficher_alerte_nj' !";
			}
		}
		else {
			if (!saveSetting("abs2_afficher_alerte_nj", "n")) {
				$msg = "Erreur lors de l'enregistrement à 'n' du paramètre 'abs2_afficher_alerte_nj' !";
			}
		}

		if (isset($_POST['abs2_afficher_alerte_nb_nj'])) {
			if(!preg_match("/^[0-9]{1,}$/", $_POST['abs2_afficher_alerte_nb_nj'])) {
				$msg = "Valeur invalide pour 'abs2_afficher_alerte_nb_nj' !";
			}
			else {
				if (!saveSetting("abs2_afficher_alerte_nb_nj", $_POST['abs2_afficher_alerte_nb_nj'])) {
					$msg = "Erreur lors de l'enregistrement du paramètre 'abs2_afficher_alerte_nb_nj' !";
				}
			}
		}

		if (isset($_POST['abs2_afficher_alerte_nj_delai'])) {
			if((!preg_match("/^[0-9]{1,}$/", $_POST['abs2_afficher_alerte_nj_delai']))||($_POST['abs2_afficher_alerte_nj_delai']<1)) {
				$msg = "Valeur invalide pour 'abs2_afficher_alerte_nj_delai' !";
			}
			else {
				if (!saveSetting("abs2_afficher_alerte_nj_delai", $_POST['abs2_afficher_alerte_nj_delai'])) {
					$msg = "Erreur lors de l'enregistrement du paramètre 'abs2_afficher_alerte_nj_delai' !";
				}
			}
		}


		if (isset($_POST['abs2_afficher_alerte_abs'])) {
			if (!saveSetting("abs2_afficher_alerte_abs", "y")) {
				$msg = "Erreur lors de l'enregistrement à 'y' du paramètre 'abs2_afficher_alerte_abs' !";
			}
		}
		else {
			if (!saveSetting("abs2_afficher_alerte_abs", "n")) {
				$msg = "Erreur lors de l'enregistrement à 'n' du paramètre 'abs2_afficher_alerte_abs' !";
			}
		}

		if (isset($_POST['abs2_afficher_alerte_nb_abs'])) {
			if(!preg_match("/^[0-9]{1,}$/", $_POST['abs2_afficher_alerte_nb_abs'])) {
				$msg = "Valeur invalide pour 'abs2_afficher_alerte_nb_abs' !";
			}
			else {
				if (!saveSetting("abs2_afficher_alerte_nb_abs", $_POST['abs2_afficher_alerte_nb_abs'])) {
					$msg = "Erreur lors de l'enregistrement du paramètre 'abs2_afficher_alerte_nb_abs' !";
				}
			}
		}

		if (isset($_POST['abs2_afficher_alerte_abs_delai'])) {
			if((!preg_match("/^[0-9]{1,}$/", $_POST['abs2_afficher_alerte_abs_delai']))||($_POST['abs2_afficher_alerte_abs_delai']<1)) {
				$msg = "Valeur invalide pour 'abs2_afficher_alerte_abs_delai' !";
			}
			else {
				if (!saveSetting("abs2_afficher_alerte_abs_delai", $_POST['abs2_afficher_alerte_abs_delai'])) {
					$msg = "Erreur lors de l'enregistrement du paramètre 'abs2_afficher_alerte_abs_delai' !";
				}
			}
		}

		$valeur_abs2_afficher_onglet_scores="n";
		if (isset($_POST['abs2_afficher_onglet_scores'])) {
			$valeur_abs2_afficher_onglet_scores="y";
		}
		if (!saveSetting("abs2_afficher_onglet_scores", $valeur_abs2_afficher_onglet_scores)) {
			$msg = "Erreur lors de l'enregistrement de abs2_afficher_onglet_scores !";
		}

		if (isset($_POST['abs2_retard_critere_duree'])) {
			if (!saveSetting("abs2_retard_critere_duree", $_POST['abs2_retard_critere_duree'])) {
				$msg = "Erreur lors de l'enregistrement de abs2_retard_critere_duree !";
			}
		}
		if (isset($_POST['abs2_heure_demi_journee'])) {
			try {
				$heure = new DateTime($_POST['abs2_heure_demi_journee']);
				if (!saveSetting("abs2_heure_demi_journee", $heure->format('H:i'))) {
					$msg = "Erreur lors de l'enregistrement de abs2_heure_demi_journee !";
				}
			} catch (Exception $x) {
				$message_enregistrement .= "Mauvais format d'heure.<br/>";
			}
		}

		if (isset($_POST['abs2_alleger_abs_du_jour'])) {
			if (!saveSetting("abs2_alleger_abs_du_jour", $_POST['abs2_alleger_abs_du_jour'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_alleger_abs_du_jour";
			}
		} else {
			if (!saveSetting("abs2_alleger_abs_du_jour", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_alleger_abs_du_jour";
			}
		}

		if (isset($_POST['abs2_limiter_abs_date_conseil_fin_annee'])) {
			if (!saveSetting("abs2_limiter_abs_date_conseil_fin_annee", $_POST['abs2_limiter_abs_date_conseil_fin_annee'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_limiter_abs_date_conseil_fin_annee";
			}
		} else {
			if (!saveSetting("abs2_limiter_abs_date_conseil_fin_annee", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_limiter_abs_date_conseil_fin_annee";
			}
		}

		if (isset($_POST['abs2_ne_pas_afficher_saisies_englobees'])) {
			if (!saveSetting("abs2_ne_pas_afficher_saisies_englobees", $_POST['abs2_ne_pas_afficher_saisies_englobees'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_ne_pas_afficher_saisies_englobees";
			}
		} else {
			if (!saveSetting("abs2_ne_pas_afficher_saisies_englobees", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_ne_pas_afficher_saisies_englobees";
			}
		}

		if (isset($_POST['abs2_ne_pas_afficher_lignes_avec_traitement_englobant'])) {
			if (!saveSetting("abs2_ne_pas_afficher_lignes_avec_traitement_englobant", $_POST['abs2_ne_pas_afficher_lignes_avec_traitement_englobant'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_ne_pas_afficher_lignes_avec_traitement_englobant";
			}
		} else {
			if (!saveSetting("abs2_ne_pas_afficher_lignes_avec_traitement_englobant", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_ne_pas_afficher_lignes_avec_traitement_englobant";
			}
		}

		if (isset($_POST['abs2_PasDoublonMarqueurAppel'])) {
			if (!saveSetting("abs2_PasDoublonMarqueurAppel", $_POST['abs2_PasDoublonMarqueurAppel'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_PasDoublonMarqueurAppel";
			}
		} else {
			if (!saveSetting("abs2_PasDoublonMarqueurAppel", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_PasDoublonMarqueurAppel";
			}
		}

		if (isset($_POST['abs2_import_manuel_bulletin'])) {
			if (!saveSetting("abs2_import_manuel_bulletin", $_POST['abs2_import_manuel_bulletin'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_import_manuel_bulletin";
			}
		} else {
			if (!saveSetting("abs2_import_manuel_bulletin", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_import_manuel_bulletin";
			}
		}

		if (isset($_POST['abs2_saisie_prof_decale_journee'])) {
			if (!saveSetting("abs2_saisie_prof_decale_journee", $_POST['abs2_saisie_prof_decale_journee'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie décalée sur la journée pour les professeurs !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_decale_journee", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie décalée sur la journée pour les professeurs !";
			}
		}

		if (isset($_POST['abs2_saisie_prof_decale'])) {
			if (!saveSetting("abs2_saisie_prof_decale", $_POST['abs2_saisie_prof_decale'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie décalée sans limite pour les professeurs !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_decale", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie décalée sans limite pour les professeurs !";
			}
		}

		if (isset($_POST['abs2_saisie_prof_hors_cours'])) {
			if (!saveSetting("abs2_saisie_prof_hors_cours", $_POST['abs2_saisie_prof_hors_cours'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie par les professeurs hors cours prévu !";
			}
		} else {
			if (!saveSetting("abs2_saisie_prof_hors_cours", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la saisie par les professeurs hors cours prévu !";
			}
		}

		if (isset($_POST['abs2_modification_saisie_une_heure'])) {
			if (!saveSetting("abs2_modification_saisie_une_heure", $_POST['abs2_modification_saisie_une_heure'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification saisie par les professeurs dans l'heure suivant la saisie !";
			}
		} else {
			if (!saveSetting("abs2_modification_saisie_une_heure", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification saisie par les professeurs dans l'heure suivant la saisie !";
			}
		}
		if (isset($_POST['abs2_sms'])) {
			if (!saveSetting("abs2_sms", $_POST['abs2_sms'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification saisie par les professeurs dans l'heure suivant la saisie !";
			}
		} else {
			if (!saveSetting("abs2_sms", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre activation/désactivation de la modification saisie par les professeurs dans l'heure suivant la saisie !";
			}
		}
		if (isset($_POST['abs2_saisie_par_defaut_sans_manquement'])) {
			if (!saveSetting("abs2_saisie_par_defaut_sans_manquement", $_POST['abs2_saisie_par_defaut_sans_manquement'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_par_defaut_sans_manquement";
			}
		} else {
			if (!saveSetting("abs2_saisie_par_defaut_sans_manquement", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_par_defaut_sans_manquement";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_sans_manquement'])) {
			if (!saveSetting("abs2_saisie_multi_type_sans_manquement", $_POST['abs2_saisie_multi_type_sans_manquement'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_sans_manquement !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_sans_manquement", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_sans_manquement !";
			}
		}

		if (isset($_POST['abs2_saisie_par_defaut_sous_responsabilite_etab'])) {
			if (!saveSetting("abs2_saisie_par_defaut_sous_responsabilite_etab", $_POST['abs2_saisie_par_defaut_sous_responsabilite_etab'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_par_defaut_sous_responsabilite_etab !";
			}
		} else {
			if (!saveSetting("abs2_saisie_par_defaut_sous_responsabilite_etab", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_par_defaut_sous_responsabilite_etab !";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_sous_responsabilite_etab'])) {
			if (!saveSetting("abs2_saisie_multi_type_sous_responsabilite_etab", $_POST['abs2_saisie_multi_type_sous_responsabilite_etab'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_sous_responsabilite_etab !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_sous_responsabilite_etab", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_sous_responsabilite_etab !";
			}
		}

		if (isset($_POST['abs2_saisie_multi_type_non_justifiee'])) {
			if (!saveSetting("abs2_saisie_multi_type_non_justifiee", $_POST['abs2_saisie_multi_type_non_justifiee'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_non_justifiee !";
			}
		} else {
			if (!saveSetting("abs2_saisie_multi_type_non_justifiee", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_saisie_multi_type_non_justifiee !";
			}
		}

		if (isset($_POST['abs2_montrer_creneaux_precedents'])) {
			if (!saveSetting("abs2_montrer_creneaux_precedents", $_POST['abs2_montrer_creneaux_precedents'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_montrer_creneaux_precedents !";
			}
		} else {
			if (!saveSetting("abs2_montrer_creneaux_precedents", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_montrer_creneaux_precedents !";
			}
		}

		if (isset($_POST['abs2_afficher_saisies_creneau_courant'])) {
			if (!saveSetting("abs2_afficher_saisies_creneau_courant", $_POST['abs2_afficher_saisies_creneau_courant'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_afficher_saisies_creneau_courant !";
			}
		} else {
			if (!saveSetting("abs2_afficher_saisies_creneau_courant", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_afficher_saisies_creneau_courant !";
			}
		}

		if (isset($_POST['abs2_rattachement_auto_saisies_englobees'])) {
			if (!saveSetting("abs2_rattachement_auto_saisies_englobees", $_POST['abs2_rattachement_auto_saisies_englobees'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_rattachement_auto_saisies_englobees !";
			}
		} else {
			if (!saveSetting("abs2_rattachement_auto_saisies_englobees", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_rattachement_auto_saisies_englobees !";
			}
		}

		if (isset($_POST['abs2_jouer_sound_erreur'])) {
			if (!saveSetting("abs2_jouer_sound_erreur", $_POST['abs2_jouer_sound_erreur'])) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_jouer_sound_erreur !";
			}
		} else {
			if (!saveSetting("abs2_jouer_sound_erreur", 'n')) {
				$msg = "Erreur lors de l'enregistrement du paramètre abs2_jouer_sound_erreur !";
			}
		}
	}
}

// On laisse seulement un administrateur modifier les paramétrages dans cette page:
if($_SESSION["statut"]=="administrateur") {
	if (isset($_POST['classement'])) {
		if (!saveSetting("absence_classement_top", $_POST['classement'])) {
			$msg = "Erreur lors de l'enregistrement du paramètre de classement des absences (TOP 10) !";
		}
	}
	if (isset($_POST['installation_base'])) {
		// Remise à zéro de la table des droits d'accès
		$result = "";
		require '../../utilitaires/updates/access_rights.inc.php';
		// Scorie:
		//require '../../utilitaires/updates/mod_abs2.inc.php';
	}

	if (isset($_POST['is_posted']) and ($msg=='')) $msg = "Les modifications ont été enregistrées (".strftime("le %d/%m/%Y à %H:%M:%S").") !";
}

// A propos du TOP 10 : récupération du setting pour le select en bas de page
$selected10 = $selected20 = $selected30 = $selected40 = $selected50 = NULL;

if (getSettingValue("absence_classement_top") == '10') {
  $selected10 = ' selected="selected"';
} elseif (getSettingValue("absence_classement_top") == '20') {
  $selected20 = ' selected="selected"';
} elseif (getSettingValue("absence_classement_top") == '30') {
  $selected30 = ' selected="selected"';
} elseif (getSettingValue("absence_classement_top") == '40') {
  $selected40 = ' selected="selected"';
} elseif (getSettingValue("absence_classement_top") == '50') {
  $selected50 = ' selected="selected"';
}

//**************** EN-TETE *****************
// header
$titre_page = "Gestion du module absence";
require_once("../../lib/header.inc.php");
//**************** EN-TETE *****************

echo "<p class='bold'><a href='../../accueil_modules.php' title='Retour'><img src='../../images/icons/back.png' alt='Retour' class='back_link' /> Retour </a> | <a href=\"http://www.sylogix.org/projects/gepi/wiki/Abs_2\" alt='Aide' />Aide à la configuration</a>";
echo "</p>";
    if (isset ($result)) {
	    echo "<center><table width=\"80%\" border=\"1\" cellpadding=\"5\" cellspacing=\"1\" summary='Résultat de mise à jour'><tr><td><h2 align=\"center\">Résultat de la mise à jour</h2>";
	    echo $result;
	    echo "</td></tr></table></center>";
    }
?>

<?php
if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
<form action="index.php" name="form1" method="post">
<p class="center"><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></p>
<?php
}
?>

<h2>Gestion des absences par les CPE</h2>
<div style='margin-left:3em;'>
<p style="font-style: italic;">La désactivation du module de la gestion des absences n'entraîne aucune
suppression des données. Lorsque le module est désactivé, les CPE n'ont pas accès au module.</p>

<?php
echo add_token_field();

$abs2_afficher_alerte_nb_nj=getSettingValue("abs2_afficher_alerte_nb_nj");
if($abs2_afficher_alerte_nb_nj=="") {
	$abs2_afficher_alerte_nb_nj=4;
}

$abs2_afficher_alerte_nj_delai=getSettingValue("abs2_afficher_alerte_nj_delai");
if($abs2_afficher_alerte_nj_delai=="") {
	$abs2_afficher_alerte_nj_delai=30;
}

$abs2_afficher_alerte_nb_abs=getSettingValue("abs2_afficher_alerte_nb_abs");
if($abs2_afficher_alerte_nb_abs=="") {
	$abs2_afficher_alerte_nb_abs=4;
}

$abs2_afficher_alerte_abs_delai=getSettingValue("abs2_afficher_alerte_abs_delai");
if($abs2_afficher_alerte_abs_delai=="") {
	$abs2_afficher_alerte_abs_delai=30;
}

if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
<p>
	<input type="radio" id="activerY" name="activer" value="y"
	<?php if (getSettingValue("active_module_absence")=='y') echo ' checked="checked"'; ?> />
	<label for="activerY">&nbsp;Activer le module de la gestion des absences <em style='color:red'>(obsolète&nbsp;: le module n'est plus maintenu)</em></label>
</p>
<p>
	<input type="radio" id="activer2" name="activer" value="2"
	<?php if (getSettingValue("active_module_absence")=='2') echo ' checked="checked"'; ?> />
	<label for="activer2">&nbsp;Activer le module de la gestion des absences version 2</label>
</p>
<p>
	<input type="radio" id="activerN" name="activer" value="n"
	<?php if (getSettingValue("active_module_absence")=='n') echo ' checked="checked"'; ?> />
	<label for="activerN">&nbsp;Désactiver le module de la gestion des absences</label>
	<input type="hidden" name="is_posted" value="1" />
</p>
<br />
<p>
	<input type="checkbox" name="abs2_import_manuel_bulletin" id="abs2_import_manuel_bulletin" value="y"
	<?php if (getSettingValue("abs2_import_manuel_bulletin")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_import_manuel_bulletin">&nbsp;Utiliser un import (<em>manuel, gep ou sconet</em>) pour les bulletins et fiches élève.</label>
</p>
<p>
	<input type="checkbox" name="abs2_alleger_abs_du_jour" id="abs2_alleger_abs_du_jour" value="y"
	<?php if (getSettingValue("abs2_alleger_abs_du_jour")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_alleger_abs_du_jour">&nbsp;Alléger les calculs de la page absence du jour : désactive la recherche des saisies contradictoires et des présences.</label>
</p>
<p style='text-indent:-2.3em;margin-left:2.3em;'>
	<input type="checkbox" name="abs2_limiter_abs_date_conseil_fin_annee" id="abs2_limiter_abs_date_conseil_fin_annee" value="y"
	<?php if (getSettingValue("abs2_limiter_abs_date_conseil_fin_annee")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_limiter_abs_date_conseil_fin_annee">&nbsp;Pour la dernière période de l'année, arrêter les totaux d'absences à la date du conseil de classe<br /><em>(ne pas tenir compte, dans les totaux, des absences de fin d'année (après la date de conseil de classe saisie en compte <strong>scolarité</strong> dans <strong>Verrouillage des périodes</strong>))</em>.</label>
</p>
<br />
<p>
	<input type="checkbox" name="abs2_ne_pas_afficher_saisies_englobees" id="abs2_ne_pas_afficher_saisies_englobees" value="y"
	<?php if (getSettingValue("abs2_ne_pas_afficher_saisies_englobees")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_ne_pas_afficher_saisies_englobees">&nbsp;Par défaut, ne pas afficher les saisies englobées dans la page 'Absences du jour'.<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(les saisies englobées n'apparaitront qu'en le choisissant explicitement dans la page)</em></label>
</p>
<p>
	<input type="checkbox" name="abs2_ne_pas_afficher_lignes_avec_traitement_englobant" id="abs2_ne_pas_afficher_lignes_avec_traitement_englobant" value="y"
	<?php if (getSettingValue("abs2_ne_pas_afficher_lignes_avec_traitement_englobant")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_ne_pas_afficher_lignes_avec_traitement_englobant">&nbsp;Par défaut, ne pas afficher les lignes concernant des saisies déjà traitées dans la page 'Absences du jour'.<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(les saisies n'apparaitront qu'en le choisissant explicitement dans la page)</em></label>
</p>
<p style='text-indent:-2.3em;margin-left:2.3em;'>
	<input type="checkbox" name="abs2_PasDoublonMarqueurAppel" id="abs2_PasDoublonMarqueurAppel" value="y"
	<?php if (getSettingValue("abs2_PasDoublonMarqueurAppel")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_PasDoublonMarqueurAppel">&nbsp;Ne pas enregistrer un marqueur d'appel pour chaque enregistrement dans la saisie de groupe lorsque l'utilisateur <em>(professeur, cpe)</em> fait plusieurs saisies dans la même heure pour un même groupe/classe.<br />
	<em>(pour un même créneau/cours/groupe... une seul marqueur d'appel sera enregistré)</em></label>
</p>

<br />
<p>
E-mail gestion absence établissement :
<input type="text" name="gepiAbsenceEmail" size="20" value="<?php echo(getSettingValue("gepiAbsenceEmail")); ?>"/>
</p>

<br />


<p>
	<input type="checkbox" name="abs2_afficher_alerte_nj" id="abs2_afficher_alerte_nj" value="y"
	<?php if (getSettingValue("abs2_afficher_alerte_nj")=='y') echo " checked='checked'"; ?> />

	<label for="abs2_afficher_alerte_nj">&nbsp;Alerter en page d'accueil les comptes CPE lorsque plus de </label>
	<input type="text" name="abs2_afficher_alerte_nb_nj" id="abs2_afficher_alerte_nb_nj" onkeydown="clavier_2(this.id,event,1,50);" value="<?php echo $abs2_afficher_alerte_nb_nj;?>" size="2" />
	<label for="abs2_afficher_alerte_nj"> manquements <em>(dans la période courante)</em> ne sont pas justifiés depuis plus de </label>
	<input type="text" name="abs2_afficher_alerte_nj_delai" id="abs2_afficher_alerte_nj_delai" onkeydown="clavier_2(this.id,event,1,300);" value="<?php echo $abs2_afficher_alerte_nj_delai;?>" size="2" />
	<label for="abs2_afficher_alerte_nj"> jours.</label>
</p>

<p>
	<input type="checkbox" name="abs2_afficher_alerte_abs" id="abs2_afficher_alerte_abs" value="y"
	<?php if (getSettingValue("abs2_afficher_alerte_abs")=='y') echo " checked='checked'"; ?> />

	<label for="abs2_afficher_alerte_abs">&nbsp;Alerter en page d'accueil les comptes CPE lorsque plus de </label>
	<input type="text" name="abs2_afficher_alerte_nb_abs" id="abs2_afficher_alerte_nb_abs" onkeydown="clavier_2(this.id,event,1,50);" value="<?php echo $abs2_afficher_alerte_nb_abs;?>" size="2" />
	<label for="abs2_afficher_alerte_abs"> manquements <em>(dans la période courante) (justifiés ou non)</em> depuis plus de </label>
	<input type="text" name="abs2_afficher_alerte_abs_delai" id="abs2_afficher_alerte_abs_delai" onkeydown="clavier_2(this.id,event,1,300);" value="<?php echo $abs2_afficher_alerte_abs_delai;?>" size="2" />
	<label for="abs2_afficher_alerte_abs"> jours.</label>
</p>


<p>
	<input type="checkbox" name="abs2_afficher_onglet_scores" id="abs2_afficher_onglet_scores" value="y"
	<?php if (!getSettingANon("abs2_afficher_onglet_scores")) echo " checked='checked'"; ?> />
	<label for="abs2_afficher_onglet_scores">&nbsp;Afficher l'onglet Scores <em>(un temps utilisé pour la note de Vie Scolaire)</em></label>
</p>


<?php
}
else {
	// NON-ADMINISTRATEUR
?>
<p>
	<?php if(getSettingValue("active_module_absence")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="activerY">&nbsp;Activer le module de la gestion des absences <em style='color:red'>(obsolète&nbsp;: le module n'est plus maintenu)</em></label>
</p>
<p>
	<?php if(getSettingValue("active_module_absence")=='2') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="activer2">&nbsp;Activer le module de la gestion des absences version 2</label>
</p>
<p>
	<?php if(getSettingValue("active_module_absence")=='n') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="activerN">&nbsp;Désactiver le module de la gestion des absences</label>
	<!--input type="hidden" name="is_posted" value="1" /-->
</p>
<br />
<p>
	<?php
	if(getSettingValue("abs2_import_manuel_bulletin")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_import_manuel_bulletin">&nbsp;Utiliser un import (<em>manuel, gep ou sconet</em>) pour les bulletins et fiches élève.</label>

</p>
<p>
	<?php
	if(getSettingValue("abs2_alleger_abs_du_jour")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_alleger_abs_du_jour">&nbsp;Alléger les calculs de la page absence du jour : désactive la recherche des saisies contradictoires et des présences.</label>
</p>
<p style='text-indent:-2.3em;margin-left:2.3em;'>
	<?php
	if(getSettingValue("abs2_limiter_abs_date_conseil_fin_annee")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_limiter_abs_date_conseil_fin_annee">&nbsp;Pour la dernière période de l'année, arrêter les totaux d'absences à la date du conseil de classe<br /><em>(ne pas tenir compte, dans les totaux, des absences de fin d'année (après la date de conseil de classe saisie en compte <strong>scolarité</strong> dans <strong>Verrouillage des périodes</strong>))</em>.</label>
</p>
<br />
<p>
	<?php
	if(getSettingValue("abs2_ne_pas_afficher_saisies_englobees")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_ne_pas_afficher_saisies_englobees">&nbsp;Par défaut, ne pas afficher les saisies englobées dans la page 'Absences du jour'.<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(les saisies englobées n'apparaitront qu'en le choisissant explicitement dans la page)</em></label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_ne_pas_afficher_lignes_avec_traitement_englobant")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_ne_pas_afficher_lignes_avec_traitement_englobant">&nbsp;Par défaut, ne pas afficher les lignes concernant des saisies déjà traitées dans la page 'Absences du jour'.<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<em>(les saisies n'apparaitront qu'en le choisissant explicitement dans la page)</em></label>
</p>
<p style='text-indent:-2.3em;margin-left:2.3em;'>
	<?php
	if(getSettingValue("abs2_PasDoublonMarqueurAppel")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_PasDoublonMarqueurAppel">&nbsp;Ne pas enregistrer un marqueur d'appel pour chaque enregistrement dans la saisie de groupe lorsque l'utilisateur <em>(professeur, cpe)</em> fait plusieurs saisies dans la même heure pour un même groupe/classe.<br />
	<em>(pour un même créneau/cours/groupe... une seul marqueur d'appel sera enregistré)</em></label>
</p>

<br />
<p>
E-mail gestion absence établissement : <?php echo(getSettingValue("gepiAbsenceEmail")); ?>
</p>

<br />

<p>
	<?php
	if(getSettingValue("abs2_afficher_alerte_nj")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_afficher_alerte_nj">&nbsp;Alerter en page d'accueil les comptes CPE lorsque plus de </label>
	<?php echo $abs2_afficher_alerte_nb_nj;?>
	<label for="abs2_afficher_alerte_nj"> manquements <em>(dans la période courante)</em> ne sont pas justifiés depuis plus de </label>
	<?php echo $abs2_afficher_alerte_nj_delai;?>
	<label for="abs2_afficher_alerte_nj"> jours.</label>
</p>

<p>
	<?php
	if(getSettingValue("abs2_afficher_alerte_abs")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_afficher_alerte_abs">&nbsp;Alerter en page d'accueil les comptes CPE lorsque plus de </label>
	<?php echo $abs2_afficher_alerte_nb_abs;?>
	<label for="abs2_afficher_alerte_abs"> manquements <em>(dans la période courante) (justifiés ou non)</em> depuis plus de </label>
	<?php echo $abs2_afficher_alerte_abs_delai;?>
	<label for="abs2_afficher_alerte_abs"> jours.</label>
</p>


<p>
	<?php
	if(!getSettingANon("abs2_afficher_onglet_scores")) {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_afficher_onglet_scores">&nbsp;Afficher l'onglet Scores <em>(un temps utilisé pour la note de Vie Scolaire)</em></label>
</p>

<?php
}
//==========================================================================
?>
</div>

<h2>Saisie des absences par les professeurs</h2>
<div style='margin-left:3em;'>
<p style="font-style: italic;">La désactivation du module de la gestion des absences n'entraîne aucune suppression des données saisies par les professeurs. Lorsque le module est désactivé, les professeurs n'ont pas accès au module.
Normalement, ce module ne devrait être activé que si le module ci-dessus est lui-même activé.</p>

<?php
if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
<p>
	<input type="radio" id="activerProfY" name="activer_prof" value="y"
	<?php if (getSettingValue("active_module_absence_professeur")=='y') echo " checked='checked'"; ?> />
	<label for="activerProfY">&nbsp;Activer le module de la saisie des absences par les professeurs</label>
</p>
<p>
	<input type="radio" id="activerProfN" name="activer_prof" value="n"
	<?php if (getSettingValue("active_module_absence_professeur")=='n') echo " checked='checked'"; ?> />
	<label for="activerProfN">&nbsp;Désactiver le module de la saisie des absences par les professeurs</label>
	<input type="hidden" name="is_posted" value="1" />
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_decale_journee" id="abs2_saisie_prof_decale_journee" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_decale_journee")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_decale_journee">&nbsp;Permettre la saisie décalée sur une même journée par les professeurs</label>
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_decale" id="abs2_saisie_prof_decale" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_decale")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_decale">&nbsp;Permettre la saisie décalée sans limite de temps par les professeurs</label>
</p>
<p>
	<input type="checkbox" name="abs2_saisie_prof_hors_cours" id="abs2_saisie_prof_hors_cours" value="y"
	<?php if (getSettingValue("abs2_saisie_prof_hors_cours")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_prof_hors_cours">&nbsp;Permettre la saisie d'une absence hors des cours prévus dans l'emploi du temps du professeur</label>
</p>
<p>
	<input type="checkbox" name="abs2_modification_saisie_une_heure" id="abs2_modification_saisie_une_heure" value="y"
	<?php if (getSettingValue("abs2_modification_saisie_une_heure")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_modification_saisie_une_heure">&nbsp;Permettre la modification d'une saisie par le professeur dans l'heure qui a suivi sa création</label>
</p>
<p>
	<input type="checkbox" name="abs2_montrer_creneaux_precedents" id="abs2_montrer_creneaux_precedents" value="y" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage."
	<?php if (getSettingValue("abs2_montrer_creneaux_precedents")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_montrer_creneaux_precedents" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.">&nbsp;Montrer les informations des créneaux précédents lors de la saisie </label>
</p>
<p>
	<input type="checkbox" name="abs2_afficher_saisies_creneau_courant" id="abs2_afficher_saisies_creneau_courant" value="y" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage."
	<?php if (getSettingValue("abs2_afficher_saisies_creneau_courant")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_afficher_saisies_creneau_courant" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.">&nbsp;Afficher en rouge le créneau en cours de saisie s'il existe déjà une autre saisie</label>
</p>
<p>
<?php if ((getSettingValue("abs2_montrer_creneaux_precedents")=='y') or (getSettingValue("abs2_afficher_saisies_creneau_courant")=='y')) echo "<p style='color:red'> VOUS AVEZ COCHÉ UNE DES DEUX CASES CI-DESSUS : l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.";?></p>
</p>

<p>
	<input type="checkbox" name="abs2_rattachement_auto_saisies_englobees" id="abs2_rattachement_auto_saisies_englobees" value="y"
	<?php if (getSettingAOui("abs2_rattachement_auto_saisies_englobees")) echo " checked='checked'"; ?> />
	<label for="abs2_rattachement_auto_saisies_englobees">&nbsp;Rattacher automatiquement les saisies englobées lors de la saisie sur le groupe.</label>
</p>

<p>
	<input type="checkbox" name="abs2_jouer_sound_erreur" id="abs2_jouer_sound_erreur" value="y"
	<?php if (getSettingAOui("abs2_jouer_sound_erreur")) echo " checked='checked'"; ?> />
	<label for="abs2_jouer_sound_erreur">&nbsp;Jouer un son en cas d'erreur d'enregistrement de la saisie sur le groupe</label>
</p>
<?php
}
else {
	// NON-ADMINISTRATEUR
?>

<p>
	<?php
	if(getSettingValue("active_module_absence_professeur")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="activerProfY">&nbsp;Activer le module de la saisie des absences par les professeurs</label>
</p>
<p>
	<?php
	if(getSettingValue("active_module_absence_professeur")=='n') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="activerProfN">&nbsp;Désactiver le module de la saisie des absences par les professeurs</label>
	<input type="hidden" name="is_posted" value="1" />
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_prof_decale_journee")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_prof_decale_journee">&nbsp;Permettre la saisie décalée sur une même journée par les professeurs</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_prof_decale")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_prof_decale">&nbsp;Permettre la saisie décalée sans limite de temps par les professeurs</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_prof_hors_cours")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_prof_hors_cours">&nbsp;Permettre la saisie d'une absence hors des cours prévus dans l'emploi du temps du professeur</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_modification_saisie_une_heure")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_modification_saisie_une_heure">&nbsp;Permettre la modification d'une saisie par le professeur dans l'heure qui a suivi sa création</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_montrer_creneaux_precedents")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_montrer_creneaux_precedents" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.">&nbsp;Montrer les informations des créneaux précédents lors de la saisie </label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_afficher_saisies_creneau_courant")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_afficher_saisies_creneau_courant" title="ATTENTION : Si vous cochez cette case, l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.">&nbsp;Afficher en rouge le créneau en cours de saisie s'il existe déjà une autre saisie</label>
</p>
<p>
<?php if ((getSettingValue("abs2_montrer_creneaux_precedents")=='y') or (getSettingValue("abs2_afficher_saisies_creneau_courant")=='y')) echo "<p style='color:red'> VOUS AVEZ COCHÉ UNE DES DEUX CASES CI-DESSUS : l'affichage de ces informations au moment de l'appel professeur est susceptible de fausser son jugement. Il est possible que l'enseignant se fie uniquement à ces informations (sans effectuer un contrôle visuel effectif) et que son appel soit erroné. Sa responsabilité pourrait être engagée. Vous pouvez-vous rapprocher de votre chef d'établissement afin de convenir de ce réglage.";?></p>
</p>

<p>
	<?php
	if(getSettingValue("abs2_rattachement_auto_saisies_englobees")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_rattachement_auto_saisies_englobees">&nbsp;Rattacher automatiquement les saisies englobées lors de la saisie sur le groupe.</label>
</p>

<p>
	<?php
	if(getSettingValue("abs2_jouer_sound_erreur")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_jouer_sound_erreur">&nbsp;Jouer un son en cas d'erreur d'enregistrement de la saisie sur le groupe</label>
</p>

<?php
}
//==========================================================================
?>
</div>

<h2>Envoi des SMS</h2>
<div style='margin-left:3em;'>
<?php
if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
	<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'>
		<em>NOTE 1&nbsp;:</em> Le paramétrage de l'envoi de SMS est à définir dans le module de <a href="../../gestion/param_gen.php#config_envoi_sms">Configuration générale.</a>
	</p>

	<?php
	if (getSettingAOui("autorise_envoi_sms"))
		{
	?>
		<p>
			<input type="checkbox" id="abs2_sms" name="abs2_sms" value="y"
			<?php if (getSettingAOui("abs2_sms")) echo " checked='checked'"; ?> />
			<label for="abs2_sms">&nbsp;Activer l'envoi des SMS</label>
		</p>
		<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'>
			<em>NOTE 2&nbsp;:</em> Le fichier modèle de SMS, comme les fichiers modèles openDocument générés par ce module peuvent être modifiés/remplacés dans la rubrique <a href="../../mod_ooo/gerer_modeles_ooo.php#MODULE_ABSENCE">Gérer ses propres modèles de documents du module</a>.
		</p>
	<?php
		}
	?>

<?php
}
else {
	// NON-ADMINISTRATEUR
?>

	<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'>
		<em>NOTE 1&nbsp;:</em> Le paramétrage de l'envoi de SMS est à définir dans le module de <strong>Configuration générale.</strong>
	</p>

	<?php
	if (getSettingAOui("autorise_envoi_sms"))
		{

		if(getSettingValue("abs2_sms")=='y') {
			echo "
		<p>
			<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
		}
		else {
			echo "
		<p>
			<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
		}
	?>
			<label for="abs2_sms">&nbsp;Activer l'envoi des SMS</label>
		</p>
		<p style='text-indent:-4em;margin-left:4em;margin-top:1em;'>
			<em>NOTE 2&nbsp;:</em> Le fichier modèle de SMS, comme les fichiers modèles openDocument générés par ce module peuvent être modifiés/remplacés dans la rubrique <?php
			if((acces("/mod_ooo/gerer_modeles_ooo.php",$_SESSION["statut"]))&&
			(($_SESSION["statut"]=="cpe")&&(getSettingAOui("OOoUploadCpeAbs2"))||
			(($_SESSION["statut"]=="scolarite")&&(getSettingAOui("OOoUploadScolAbs2"))))) {
				echo "<a href='../../mod_ooo/gerer_modeles_ooo.php#MODULE_ABSENCE'>Gérer ses propres modèles de documents du module</a>";
			}
			else {
				echo "<strong>Gérer ses propres modèles de documents du module</strong>";
			}
		?>.
		</p>
	<?php
		}
	?>


<?php
}
//==========================================================================
?>
</div>

<h2>Configuration des saisies</h2>
<div style='margin-left:3em;'>
<?php
if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
<p>
	<input type="checkbox" id="abs2_saisie_par_defaut_sans_manquement" name="abs2_saisie_par_defaut_sans_manquement" value="y"
	<?php if (getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_par_defaut_sans_manquement">&nbsp;Dans le cas d'une saisie sans type, considérer que l'élève ne manque pas à ses obligations.
	   (Donc ces saisies ne seront pas comptées dans les bulletins)</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_sans_manquement" name="abs2_saisie_multi_type_sans_manquement" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_sans_manquement">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que l'élève ne manque pas à ses obligations.
	   (Donc ces saisies ne seront pas comptées dans les bulletins)</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_non_justifiee" name="abs2_saisie_multi_type_non_justifiee" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_non_justifiee")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_non_justifiee">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que la saisie n'est pas justifiée.</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_par_defaut_sous_responsabilite_etab" name="abs2_saisie_par_defaut_sous_responsabilite_etab" value="y"
	<?php if (getSettingValue("abs2_saisie_par_defaut_sous_responsabilite_etab")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_par_defaut_sous_responsabilite_etab">&nbsp;Dans le cas d'une saisie sans type, considérer que l'élève est par défaut sous la responsabilité de l'établissement.</label>
</p>
<p>
	<input type="checkbox" id="abs2_saisie_multi_type_sous_responsabilite_etab" name="abs2_saisie_multi_type_sous_responsabilite_etab" value="y"
	<?php if (getSettingValue("abs2_saisie_multi_type_sous_responsabilite_etab")=='y') echo " checked='checked'"; ?> />
	<label for="abs2_saisie_multi_type_sous_responsabilite_etab">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que l'élève est par défaut sous la responsabilité de l'établissement.</label>
</p>
<p>
	<?php if (getSettingValue("abs2_retard_critere_duree") == null || getSettingValue("abs2_retard_critere_duree") == '') saveSetting("abs2_retard_critere_duree", 30); ?>
	Configuration du bulletin : Dans le décompte des demi-journées d'absence, considérer les saisie inférieures à
	<select name="abs2_retard_critere_duree">
		<option value="00" <?php if (getSettingValue("abs2_retard_critere_duree") == '00') echo " selected"; ?>>00</option>
		<option value="10" <?php if (getSettingValue("abs2_retard_critere_duree") == '10') echo " selected"; ?>>10</option>
		<option value="20" <?php if (getSettingValue("abs2_retard_critere_duree") == '20') echo " selected"; ?>>20</option>
		<option value="30" <?php if (getSettingValue("abs2_retard_critere_duree") == '30') echo " selected"; ?>>30</option>
		<option value="40" <?php if (getSettingValue("abs2_retard_critere_duree") == '40') echo " selected"; ?>>40</option>
		<option value="50" <?php if (getSettingValue("abs2_retard_critere_duree") == '50') echo " selected"; ?>>50</option>
	</select>
	min comme des retards.<br/>
	Note : si les créneaux durent 45 minutes et que ce paramètre est réglé sur 50 min, la plupart de vos saisies seront décomptées comme retard.<br/>
	Note : sont considérées comme retard les saisies de durées inférieures au paramètre ci-dessus et les saisies dont le type est décompté comme retard
	(voir la page <a href="admin_types_absences.php?action=visualiser">Définir les types d'absence</a>).<br/>

</p>
<br/>
<p>
	<?php if (getSettingValue("abs2_heure_demi_journee") == null || getSettingValue("abs2_heure_demi_journee") == '') saveSetting("abs2_heure_demi_journee", '11:55'); ?>
	<input style="font-size:88%;" name="abs2_heure_demi_journee" value="<?php echo getSettingValue("abs2_heure_demi_journee")?>" type="text" maxlength="5" size="4"/>
	Heure de bascule de demi-journée pour le décompte des demi-journées. Cette heure doit correspondre au tout début de la pause déjeuner <em>(typiquement 11:55 ou 12:25)</em>.<br />
	En cas de correction/modification de cette valeur, n'oubliez pas de vider/re-remplir la table d'agrégation des absences pour recalculer les absences sur telle ou telle demi-journée.
</p>

<!--h2>G&eacute;rer l'acc&egrave;s des responsables d'&eacute;l&egrave;ves</h2>
<p style="font-style: italic">Vous pouvez permettre aux responsables d'acc&eacute;der aux donn&eacute;es brutes
entr&eacute;es dans Gepi par le biais du module absences.</p>
<p>
	<input type="radio" id="activerRespOk" name="activer_resp" value="y"
	<?php if (getSettingValue("active_absences_parents") == 'y') echo ' checked="checked"'; ?> />
	<label for="activerRespOk">Permettre l'acc&egrave;s aux responsables</label>
</p>
<p>
	<input type="radio" id="activerRespKo" name="activer_resp" value="n"
	<?php if (getSettingValue("active_absences_parents") == 'n') echo ' checked="checked"'; ?> />
	<label for="activerRespKo">Ne pas permettre cet acc&egrave;s</label>
</p-->

<?php
}
else {
	// NON-ADMINISTRATEUR
?>

<p>
	<?php
	if(getSettingValue("abs2_saisie_par_defaut_sans_manquement")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_par_defaut_sans_manquement">&nbsp;Dans le cas d'une saisie sans type, considérer que l'élève ne manque pas à ses obligations.
	   (Donc ces saisies ne seront pas comptées dans les bulletins)</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_multi_type_sans_manquement")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_multi_type_sans_manquement">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que l'élève ne manque pas à ses obligations.
	   (Donc ces saisies ne seront pas comptées dans les bulletins)</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_multi_type_non_justifiee")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_multi_type_non_justifiee">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que la saisie n'est pas justifiée.</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_par_defaut_sous_responsabilite_etab")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_par_defaut_sous_responsabilite_etab">&nbsp;Dans le cas d'une saisie sans type, considérer que l'élève est par défaut sous la responsabilité de l'établissement.</label>
</p>
<p>
	<?php
	if(getSettingValue("abs2_saisie_multi_type_sous_responsabilite_etab")=='y') {
		echo "<img src='../../images/enabled.png' class='icone20' alt='Coché' />";
	}
	else {
		echo "<img src='../../images/disabled.png' class='icone20' alt='Non coché' />";
	}
	?>
	<label for="abs2_saisie_multi_type_sous_responsabilite_etab">&nbsp;Dans le cas de plusieurs saisies simultanées avec des types contradictoires, considérer que l'élève est par défaut sous la responsabilité de l'établissement.</label>
</p>
<p>
	<?php if (getSettingValue("abs2_retard_critere_duree") == null || getSettingValue("abs2_retard_critere_duree") == '') saveSetting("abs2_retard_critere_duree", 30); ?>
	Configuration du bulletin : Dans le décompte des demi-journées d'absence, considérer les saisie inférieures à 
	<?php echo getSettingValue("abs2_retard_critere_duree");?>
	min comme des retards.<br/>
	Note : si les créneaux durent 45 minutes et que ce paramètre est réglé sur 50 min, la plupart de vos saisies seront décomptées comme retard.<br/>
	Note : sont considérées comme retard les saisies de durées inférieures au paramètre ci-dessus et les saisies dont le type est décompté comme retard
	<?php 
		if((acces_consultation_admin_abs2("/mod_abs2/admin/admin_types_absences.php"))||
		(acces_saisie_admin_abs2("/mod_abs2/admin/admin_types_absences.php"))) {
			echo "(voir la page <a href='admin_types_absences.php?action=visualiser'>Définir les types d'absence</a>)";
		}
		else {
			echo "(voir la page <strong>Définir les types d'absence</strong>)";
		}
	?>
	.<br/>

</p>
<br/>
<p>
	<?php if (getSettingValue("abs2_heure_demi_journee") == null || getSettingValue("abs2_heure_demi_journee") == '') saveSetting("abs2_heure_demi_journee", '11:55'); ?>
	<?php echo getSettingValue("abs2_heure_demi_journee")?> 
	Heure de bascule de demi-journée pour le décompte des demi-journées. Cette heure doit correspondre au tout début de la pause déjeuner <em>(typiquement 11:55 ou 12:25)</em>.<br />
	En cas de correction/modification de cette valeur, n'oubliez pas de vider/re-remplir la table d'agrégation des absences pour recalculer les absences sur telle ou telle demi-journée.
</p>

<!--h2>G&eacute;rer l'acc&egrave;s des responsables d'&eacute;l&egrave;ves</h2>
<p style="font-style: italic">Vous pouvez permettre aux responsables d'acc&eacute;der aux donn&eacute;es brutes
entr&eacute;es dans Gepi par le biais du module absences.</p>
<p>
	<input type="radio" id="activerRespOk" name="activer_resp" value="y"
	<?php if (getSettingValue("active_absences_parents") == 'y') echo ' checked="checked"'; ?> />
	<label for="activerRespOk">Permettre l'acc&egrave;s aux responsables</label>
</p>
<p>
	<input type="radio" id="activerRespKo" name="activer_resp" value="n"
	<?php if (getSettingValue("active_absences_parents") == 'n') echo ' checked="checked"'; ?> />
	<label for="activerRespKo">Ne pas permettre cet acc&egrave;s</label>
</p-->

<?php
}
//==========================================================================
?>

</div>

<br/>
<?php
if($_SESSION["statut"]=="administrateur") {
	// ADMINISTRATEUR
?>
<p class="center"><input type="submit" value="Enregistrer" style="font-variant: small-caps;"/></p>
</form>
<?php
}
?>
<?php
echo "<p style='color:red' font-style:bold> LES RESPONSABILITÉS&nbsp;: <br /><br /></p>";
echo "<p style='color:red'>* Le responsable de l'absence, c'est l'élève (et ses parents).<br />* Le responsable de la <b>gestion</b> (ou traitement) de l'absence, c'est la vie scolaire.<br />* Le responsable du <b>constat</b> de l'absence, c'est l'enseignant (pour un cours, ou l'adulte pour une activité encadrée).<br />Si la gestion anticipe une absence, elle peut communiquer l'information, mais cela ne vaut pas constat, lequel devient alors validation de l'anticipation, mais reste indispensable.</p>";
?>
<br/><br/>
<a name='config_avancee'></a>
<h2>Configuration avancée</h2>
<blockquote>
<?php
	if((acces_consultation_admin_abs2("/mod_abs2/admin/admin_types_absences.php"))||
	(acces_saisie_admin_abs2("/mod_abs2/admin/admin_types_absences.php"))) {
		echo "<a href='admin_types_absences.php?action=visualiser'>Définir les types d'absence</a><br />";
	}
	else {
		echo "<strong style='color:grey' title='Accès non autorisé'>Définir les types d'absence</strong><br />";
	}

	if((acces_consultation_admin_abs2("/mod_abs2/admin/admin_motifs_absences.php"))||
	(acces_saisie_admin_abs2("/mod_abs2/admin/admin_motifs_absences.php"))) {
		echo "<a href='admin_motifs_absences.php?action=visualiser'>Définir les motifs des absences</a><br />";
	}
	else {
		echo "<strong style='color:grey'>Définir les motifs des absences</strong><br />";
	}

	if((acces_consultation_admin_abs2("/mod_abs2/admin/admin_lieux_absences.php"))||
	(acces_saisie_admin_abs2("/mod_abs2/admin/admin_lieux_absences.php"))) {
		echo "	<a href='admin_lieux_absences.php?action=visualiser'>Définir les lieux des absences</a><br />";
	}
	else {
		echo "<strong style='color:grey' title='Accès non autorisé'>Définir les lieux des absences</strong><br />";
	}

	if((acces_consultation_admin_abs2("/mod_abs2/admin/admin_justifications_absences.php"))||
	(acces_saisie_admin_abs2("/mod_abs2/admin/admin_justifications_absences.php"))) {
		echo "	<a href='admin_justifications_absences.php?action=visualiser'>Définir les justifications</a><br />";
	}
	else {
		echo "<strong style='color:grey' title='Accès non autorisé'>Définir les justifications</strong><br />";
	}

	if(($_SESSION["statut"]=="administrateur")||
	(($_SESSION["statut"]=="cpe")&&(getSettingAOui("OOoUploadCpeAbs2")))) {
		echo "	<a href='../../mod_ooo/gerer_modeles_ooo.php#MODULE_ABSENCE'>Gérer ses propres modèles de documents du module</a><br />";
	}
	else {
		echo "<strong style='color:grey' title='Accès non autorisé'>Gérer ses propres modèles de documents du module</strong><br />";
	}

	if(($_SESSION["statut"]=="administrateur")||
	(($_SESSION["statut"]=="cpe")&&(getSettingAOui("AccesCpeAgregationAbs2")))) {
		echo "	<a href='admin_table_agregation.php'>Gérér la table d'agrégation des demi-journées d'absences</a><br />";
	}
	else {
		echo "<strong style='color:grey' title='Accès non autorisé'>Gérér la table d'agrégation des demi-journées d'absences</strong><br />";
	}
?>
	<?php
		if(acces("/mod_abs2/admin/admin_table_totaux_absences.php", $_SESSION['statut'])) {
			if(getSettingAOui('abs2_import_manuel_bulletin')) {
				echo '<span style="color:red" title="Fonction désactivée:
Elle sert à remplir la table \'absences\' (totaux d\'absences, retards,... 
pour les bulletins et pour Genèse des classes) d\'après les saisies
du module Absences 2 compilées dans la table \'a_agregation_decompte\'.
Mais vous n\'extrayez pas les absences du mod_abs2.
Pas question d\'écraser des saisies/import effectués autrement.">Gérér la table des totaux d\'absences</span>';
			}
			else {
				echo '<a href="admin_table_totaux_absences.php">Gérér la table des totaux d\'absences</a>';
			}
			echo "<br />";
		}

		if($_SESSION['statut']=="administrateur") {
			echo "<a href=\"admin_droits.php\">Donner des droits d'accès à des pages d'administration du module Absences 2</a><br />";
		}
	?>
</blockquote>

<h2>Partie Emplois du temps</h2>
<blockquote>
<?php
		if($_SESSION['statut']=="administrateur") {
?>
	<a href="../../edt_organisation/admin_horaire_ouverture.php?action=visualiser">Définir les horaires d'ouverture de l'établissement</a><br />
	<a href="../../edt_organisation/admin_periodes_absences.php?action=visualiser">Définir les créneaux horaires</a><br />
	<?php // On vérifie si le module calendrier / edt est ouvert ou non pour savoir quel lien on lance

	if ((getSettingAOui('autorise_edt_admin'))||getSettingAOui('autorise_edt_tous')) {
		// On initialise le $_SESSION["retour"] pour pouvoir revenir proprement
		$_SESSION["retour"] = "../mod_abs2/admin/index";
		echo '<a href="../../edt_organisation/edt_calendrier.php">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</a><br />';
	} else {
		echo '<a href="../../mod_absences/admin/admin_config_calendrier.php?action=visualiser">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</a><br />';
	}
	?>
	<a href="../../edt_organisation/admin_config_semaines.php?action=visualiser">Définir les types de semaine</a><br />
<?php
	}
	else {
?>
	<strong style="color:grey" title='Accès non autorisé'>Définir les horaires d'ouverture de l'établissement</strong><br />
	<strong style="color:grey" title='Accès non autorisé'>Définir les créneaux horaires</strong><br />
	<?php // On vérifie si le module calendrier / edt est ouvert ou non pour savoir quel lien on lance

	if ((getSettingAOui('autorise_edt_admin'))||getSettingAOui('autorise_edt_tous')) {
		// On initialise le $_SESSION["retour"] pour pouvoir revenir proprement
		$_SESSION["retour"] = "../mod_abs2/admin/index";
		echo '<strong style="color:grey" title="Accès non autorisé">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</strong><br />';
	} else {
		echo '<strong style="color:grey" title="Accès non autorisé">D&eacute;finir p&eacute;riodes de vacances et jours f&eacute;ri&eacute;s</strong><br />';
	}
	?>
	<strong style="color:grey" title='Accès non autorisé'>Définir les types de semaine</strong><br />
<?php	}
?>
</blockquote>

<?php
require("../../lib/footer.inc.php");
?>
