<?php
/*
 *
 * Copyright 2001, 2013 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

// Begin standart header
$titre_page = "Accueil - Administration des modules";
$racine_gepi = 'yes';
$affiche_connexion = 'yes';
$niveau_arbo = 0;
$gepiPathJava=".";

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

if (!checkAccess()) {
	header("Location: ./logout.php?auto=1");
	die();
}

include "class_php/class_menu_general.php";

$tab[0] = "administrateur";
$tab[1] = "professeur";
$tab[2] = "cpe";
$tab[3] = "scolarite";
$tab[4] = "eleve";
$tab[5] = "secours";

// ====== Inclusion des balises head et du bandeau =====
 //$msg = "Essai message";

	include_once("./lib/header_template.inc.php");
/****************************************************************
			FIN HAUT DE PAGE
****************************************************************/
if (!suivi_ariane($_SERVER['PHP_SELF'],"Administration des modules"))
		echo "erreur lors de la création du fil d'ariane";
/****************************************************************

****************************************************************/

/* ===== Titre du menu ===== */
	$menuTitre=array();
	$menuTitre[]=new menuGeneral;
	end($menuTitre);
	$a = key($menuTitre);
	$menuTitre[$a]->classe='accueil';
	$menuTitre[$a]->icone['chemin']='./images/icons/control-center.png';
	$menuTitre[$a]->icone['titre']='';
	$menuTitre[$a]->icone['alt']="Admin modules";
	$menuTitre[$a]->texte='Administration des modules';

/* ===== Item du menu ===== */
	$menuPage=array();
	
// cahier de texte
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_texte_admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_cahiers_texte') ;	
		$nouveauItem->titre="Cahier de textes" ;
		$nouveauItem->expli="Pour gérer les cahiers de texte, (configuration générale, ...)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);	
	
// cahier de notes
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_notes_admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_carnets_notes') ;	
		$nouveauItem->titre="Carnets de notes" ;
		$nouveauItem->expli="Pour gérer les carnets de notes (configuration générale, ...)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Bulletins
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/bulletin/index_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_bulletins') ;	
		$nouveauItem->titre="Bulletins" ;
		$nouveauItem->expli="Pour gérer le module bulletins" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Absences
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_absences/admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_absence%',"mod_absences") ;	
		$nouveauItem->titre="Absences" ;
		$nouveauItem->expli="Pour gérer le module absences<br /><span style='color:red'>Ce module n'est plus maintenu.</span><br />Vous ne pourrez pas obtenir d'aide des développeurs sur ce module.<br />Utilisez plutôt le module absences2 ci-dessous." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Absences 2
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_abs2/admin/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_absence',"mod_abs2") ;	
		$nouveauItem->titre="Absences 2" ;
		$nouveauItem->expli="Pour gérer le module absences 2" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Absences/remplacements de professeurs
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_abs_prof/index_admin.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone("active_mod_abs_prof") ;
		$nouveauItem->titre="Absences/remplacements professeurs" ;
		$nouveauItem->expli="Pour gérer le module des saisie des absences et remplacements ponctuels de professeurs" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Emploi du temps
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/edt_organisation/edt.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('autorise_edt%') ;	
		$nouveauItem->titre="Emploi du temps" ;
		$nouveauItem->expli="Pour gérer l'ouverture de l'emploi du temps de Gepi." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Emploi du temps ICAL/ICS
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/edt/index_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('autorise_edt%') ;	
		$nouveauItem->titre="Emploi du temps ICAL/ICS" ;
		$nouveauItem->expli="Pour gérer l'ouverture et les paramètres des emplois du temps importés depuis des fichiers ICAL/ICS." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Trombinoscope
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_trombinoscopes/trombinoscopes_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_module_trombinoscopes') ;	
		$nouveauItem->titre="Trombinoscope" ;
		$nouveauItem->expli="Pour gérer le module trombinoscope." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Notanet/Fiches Brevet
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_notanet/notanet_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_notanet') ;	
		$nouveauItem->titre="Notanet/Fiches Brevet" ;
		$nouveauItem->expli="Pour gérer le module Notanet/Fiches Brevet" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Inscription
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_inscription/inscription_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_inscription') ;	
		$nouveauItem->titre="Inscription" ;
		$nouveauItem->expli="Pour gérer simplement les inscriptions des ".$gepiSettings['denomination_professeurs']." par exemple à des stages ou bien des interventions dans les collèges" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Flux rss
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/cahier_texte_admin/rss_cdt_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('rss_cdt_eleve') ;	
		$nouveauItem->titre="<img src=\"images/icons/rss.png\" alt='rss' />&nbsp;-&nbsp;Flux rss" ;
		$nouveauItem->expli="Gestion des flux rss des cahiers de textes produits par Gepi" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);


// Autorisation des statuts personnalisés
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/utilisateurs/creer_statut_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('statuts_prives') ;	
		$nouveauItem->titre="Créer des statuts personnalisés" ;
		$nouveauItem->expli="Définir des statuts supplémentaires en personnalisant les droits d'accès." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
	
// Années antérieures
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_annees_anterieures/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_annees_anterieures') ;	
		$nouveauItem->titre="Années antérieures" ;
		$nouveauItem->expli="Pour gérer le module Années antérieures." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Module ateliers
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ateliers/ateliers_config.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_ateliers') ;	
		$nouveauItem->titre="Ateliers" ;
		$nouveauItem->expli="Gestion et mise en place d'ateliers de type conférences (gestion des ateliers, des intervenants, des inscriptions...)." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Module discipline
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_discipline/discipline_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_discipline') ;	
		$nouveauItem->titre="Discipline" ;
		$nouveauItem->expli="Pour gérer le module Discipline." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module modèle Open_Office
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ooo/ooo_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_ooo') ;	
		$nouveauItem->titre="Modèle openDocument" ;
		$nouveauItem->expli="Pour gérer les modèles openDocument (libreOffice/openOffice) de Gepi." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module ECTS
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_ects/ects_admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_ects') ;	
		$nouveauItem->titre="Crédits ECTS" ;
		$nouveauItem->expli="Pour gérer les crédits ECTS attribués pour chaque enseignement." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Plugins
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_plugins/index.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('') ;	
		$nouveauItem->titre="Gérer les plugins" ;
		$nouveauItem->expli="Interface d'administration des plugins personnels de l'établissement." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Génèse des classes
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_genese_classes/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_genese_classes') ;	
		$nouveauItem->titre="Génèse des classes" ;
		$nouveauItem->expli="Pour gérer le module Génèse des classes." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Epreuve blanche
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_epreuve_blanche/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_epreuve_blanche') ;	
		$nouveauItem->titre="Epreuves blanches" ;
		$nouveauItem->expli="Pour gérer des épreuves blanches (anonymat des copies,...)." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module Examen blanc
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_examen_blanc/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_examen_blanc') ;	
		$nouveauItem->titre="Examens blancs" ;
		$nouveauItem->expli="Pour gérer des examens blancs." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

//Module "Admissions Post-Bac"
/*
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_apb/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_apb') ;	
		$nouveauItem->titre="Admissions Post-Bac" ;
		$nouveauItem->expli="Pour gérer l'export XML vers la plateforme 'Admissions post-bac'." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);
*/

//Module Gestionnaires d'AID
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_gest_aid/admin.php';	
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_gest_aid') ;	
		$nouveauItem->titre="Gestionnaires d'AID" ;
		$nouveauItem->expli="Pour ouvrir la possibilité de définir des gestionnaires pour chaque AID." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Messagerie
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_alerte/admin.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_alerte') ;
		$nouveauItem->titre="Alertes" ;
		$nouveauItem->expli="Pour gérer le dispositif d'alerte." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Engagements
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_engagements/index_admin.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_engagements') ;
		$nouveauItem->titre="Engagements" ;
		$nouveauItem->expli="Pour gérer le module Engagements (délégués de classe, représentants de parents,...)" ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Listes personnelles
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_listes_perso/index_admin.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('GepiListePersonnelles') ;
		$nouveauItem->titre="Listes personnelles" ;
		$nouveauItem->expli="Donner l'accès à la création de listes personnelles aux enseignants, CPE, scolarité." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);

// Orientation
	$nouveauItem = new itemGeneral();
	$nouveauItem->chemin='/mod_orientation/admin.php';
	if ($nouveauItem->acces($nouveauItem->chemin,$_SESSION['statut']))
	{
		$nouveauItem->choix_icone('active_mod_orientation') ;
		$nouveauItem->titre="Orientation" ;
		$nouveauItem->expli="Donner l'accès à la saisie des voeux et orientations proposées." ;
		$menuPage[]=$nouveauItem;
	}
	unset($nouveauItem);


$tbs_microtime="";
$tbs_pmv="";
require_once ("./lib/footer_template.inc.php");

/****************************************************************
			On s'assure que le nom du gabarit est bien renseigné
****************************************************************/
if ((!isset($_SESSION['rep_gabarits'])) || (empty($_SESSION['rep_gabarits']))) {
	$_SESSION['rep_gabarits']="origine";
}

//==================================
// Décommenter la ligne ci-dessous pour afficher les variables $_GET, $_POST, $_SESSION et $_SERVER pour DEBUG:
// $affiche_debug=debug_var2();


$nom_gabarit = './templates/'.$_SESSION['rep_gabarits'].'/accueil_modules_template.php';

$tbs_last_connection=""; // On n'affiche pas les dernières connexions
include($nom_gabarit);

// ------ on vide les tableaux -----
unset($menuPage);


/*
for ($i=0;$i<count($menuPage);$i++)
{
	echo "<img src='".$menuPage[$i]->icone['chemin']."' width='19' height='19' title='".$menuPage[$i]->icone['titre']."' alt='".$menuPage[$i]->icone['alt']."' />";
	echo " - ".$i." - <a href='".mb_substr($menuPage[$i]->chemin,1)."'>".$menuPage[$i]->titre."</a> - ".$menuPage[$i]->expli."<br />";
}
*/

?>
