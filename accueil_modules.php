<?php
/*
 * $Id$
 *
 * Copyright 2001, 2005 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$niveau_arbo=0;

// Initialisations files
require_once("./lib/initialisations.inc.php");

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
   die();
} else if ($resultat_session == '0') {
   header("Location: ./logout.php?auto=1");
   die();
}

$tab[0] = "administrateur";
$tab[1] = "professeur";
$tab[2] = "cpe";
$tab[3] = "scolarite";
$tab[4] = "eleve";
$tab[5] = "secours";

function acces($id,$statut) {
    $tab_id = explode("?",$id);
    $query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
    $droit = @mysql_result($query_droits, 0, $statut);
    if ($droit == "V") {
        return "1";
    } else {
        return "0";
    }
}



function affiche_ligne($chemin_,$titre_,$expli_,$tab,$statut_,$key_setting) {
    if (acces($chemin_,$statut_)==1)  {
        $temp = substr($chemin_,1);
        echo "<tr>\n";
        //echo "<td width=30%><a href=$temp>$titre_</a></span>";
        echo "<td>\n";
		if($key_setting!='') {
			$sql="SELECT 1=1 FROM setting WHERE name LIKE '$key_setting' AND (value='y' OR value='yes');";
			$test=mysql_query($sql);
			if(mysql_num_rows($test)>0) {
				echo "<img src='images/enabled.png' width='20' height='20' title='Module actif' alt='Module actif' />\n";
			}
			else {
				echo "<img src='images/disabled.png' width='20' height='20' title='Module inactif' alt='Module inactif' />\n";
			}
		}
		else {
			echo "<img src='images/icons/ico_question.png' width='19' height='19' title='Etat inconnu' alt='Etat inconnu' />\n";
		}
        echo "</td>\n";
        echo "<td width='30%'><a href=$temp>$titre_</a>";
        echo "</td>\n";
        echo "<td>$expli_</td>\n";
        echo "</tr>\n";
    }
}
if (!checkAccess()) {
	header("Location: ./logout.php?auto=1");
	die();
}
$titre_page = "Accueil - Administration des modules";
$racine_gepi = 'yes';
require_once("./lib/header.inc");
?>
<p class=bold><a href="./accueil.php"><img src='./images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
<?php if (isset($msg)) { echo "<font color='red' size=2>$msg</font>"; }
echo "<center>";

$key_setting=array('active_cahiers_texte',
'active_carnets_notes');
$chemin = array(
"/cahier_texte_admin/index.php",
"/cahier_notes_admin/index.php");

$chemin[] = "/mod_absences/admin/index.php";
$key_setting[]='active_module_absence%';
$chemin[] = "/mod_abs2/admin/index.php";
$key_setting[]='active_module_absence%';
$chemin[] = "/edt_organisation/edt.php";
$key_setting[]='autorise_edt%';

if ($force_msj) {
	$chemin[] = "/mod_miseajour/admin/index.php";
	$key_setting[]='active_module_msj';
}

$chemin[] = "/mod_trombinoscopes/trombinoscopes_admin.php";
$key_setting[]='active_module_trombinoscopes';
$chemin[] = "/mod_notanet/notanet_admin.php";
$key_setting[]='active_notanet';
$chemin[] = "/mod_inscription/inscription_admin.php";
$key_setting[]='active_inscription';
$chemin[] = "/cahier_texte_admin/rss_cdt_admin.php";
$key_setting[]='rss_cdt_eleve';

$titre = array(
"Cahier de textes",
"Carnets de notes");
$titre[] = "Absences";
$titre[] = "Absences 2";
$titre[] = "Emploi du temps";
if ($force_msj) {$titre[] = "Mise à jour automatisée";}
$titre[] = "Trombinoscope";
$titre[] = "Notanet/Fiches Brevet";
$titre[] = "Inscription";
$titre[] = "<img src=\"images/icons/rss.png\" alt='rss' />&nbsp;-&nbsp;Flux rss";

$expli = array(
"Pour gérer les cahiers de texte, (configuration générale, ...)",
"Pour gérer les carnets de notes (configuration générale, ...)");
$expli[] = "Pour gérer le module absences";
$expli[] = "Pour gérer le module absences 2 (en cours de developpement)";
$expli[] = "Pour gérer l'ouverture de l'emploi du temps de Gepi.";
if ($force_msj) {$expli[] = "Pour gérer le module de mise à jour de GEPI";}
$expli[] = "Pour gérer le module trombinoscope";
$expli[] = "Pour gérer le module Notanet/Fiches Brevet";
$expli[] = "Pour gérer simplement les inscriptions des ".$gepiSettings['denomination_professeurs']." par exemple à des stages ou bien des interventions dans les collèges";
$expli[] = "Gestion des flux rss des cahiers de textes produits par Gepi";

// AUtorisation des statuts personnalisés
// Années antérieures
$chemin[] = "/utilisateurs/creer_statut_admin.php";
$titre[] = "Créer des statuts personnalisés";
$expli[] = "Définir des statuts supplémentaires en personnalisant les droits d'accès.";
$key_setting[]='statuts_prives';

// Années antérieures
$chemin[] = "/mod_annees_anterieures/admin.php";
$titre[] = "Années antérieures";
$expli[] = "Pour gérer le module Années antérieures";
$key_setting[]='active_annees_anterieures';

// Module ateliers
$chemin[] = "/mod_ateliers/ateliers_config.php";
$titre[] = "Ateliers";
$expli[] = "Gestion et mise en place d'ateliers de type conférences (gestion des ateliers, des intervenants, des inscriptions...).";
$key_setting[]='active_ateliers';

// Module discipline
$chemin[] = "/mod_discipline/discipline_admin.php";
$titre[] = "Discipline";
$expli[] = "Pour gérer le module Discipline.";
$key_setting[]='active_mod_discipline';

//Module modèle Open_Office
$chemin[] = "/mod_ooo/ooo_admin.php";
$titre[] = "Modèle OpenOffice";
$expli[] = "Pour gérer les modèles Open Office de Gepi.";
$key_setting[]='active_mod_ooo';

//Module ECTS
$chemin[] = "/mod_ects/ects_admin.php";
$titre[] = "Saisie ECTS";
$expli[] = "Pour gérer les crédits ECTS attribués pour chaque enseignement.";
$key_setting[]='active_mod_ects';

//Module Plugins
$chemin[] = "/mod_plugins/index.php";
$titre[] = "Gérer les plugins";
$expli[] = "Interface d'administration des plugins personnels de l'établissement.";
$key_setting[]='';

//Module Génèse des classes
$chemin[] = "/mod_genese_classes/admin.php";
$titre[] = "Génèse des classes";
$expli[] = "Pour gérer le module Génèse des classes.";
$key_setting[]='active_mod_genese_classes';

//Module Epreuve blanche
$chemin[] = "/mod_epreuve_blanche/admin.php";
$titre[] = "Epreuves blanches";
$expli[] = "Pour gérer des épreuves blanches (anonymat des copies,...).";
$key_setting[]='active_mod_epreuve_blanche';

//Module Examen blanc
$chemin[] = "/mod_examen_blanc/admin.php";
$titre[] = "Examens blancs";
$expli[] = "Pour gérer des examens blancs.";
$key_setting[]='active_mod_examen_blanc';

//Module "Admissions Post-Bac"
$chemin[] = "/mod_apb/admin.php";
$titre[] = "Admissions Post-Bac";
$expli[] = "Pour gérer l'export XML vers la plateforme 'Admissions post-bac'.";
$key_setting[]='active_mod_apb';

//Module "Admissions Post-Bac"
$chemin[] = "/mod_gest_aid/admin.php";
$titre[] = "Gestionnaires d'AID";
$expli[] = "Pour ouvrir la possibilité de définir des gestionnaires pour chaque AID.";
$key_setting[]='active_mod_gest_aid';

$nb_ligne = count($chemin);
//
// Outils d'administration
//
$affiche = 'no';
for ($i=0;$i<$nb_ligne;$i++) {
    if (acces($chemin[$i],$_SESSION['statut'])==1)  {$affiche = 'yes';}
}
if ($affiche=='yes') {
    //echo "<table width=700 border=2 cellspacing=1 bordercolor=#330033 cellpadding=5>";
    echo "<table class='menu' summary='Administration des modules'>\n";
    echo "<tr>\n";
    echo "<th colspan='3'><img src='./images/icons/control-center.png' alt='Admin modules' class='link'/> - Administration des modules</th>\n";
    echo "</tr>\n";
    for ($i=0;$i<$nb_ligne;$i++) {
        affiche_ligne($chemin[$i],$titre[$i],$expli[$i],$tab,$_SESSION['statut'],$key_setting[$i]);
    }
    echo "</table>\n";
}

?>
</center>
<?php
	require("./lib/footer.inc.php");
?>
