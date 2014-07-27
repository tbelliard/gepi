<?php

/**
 * Barre de menu des pages administrateurs utilisant un gabarit
 * 
 * 
 * 
 *
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see getSettingValue()
 * @todo Réécrire la barre administrateur, le principe des gabarits, c'est d'envoyer des variables aux gabarits, 
 * pas d'écrire du code html dans le constructeur
 */

 /*
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
 *
 * Fichier qui permet de construire la barre de menu professeur
 *
 */
  
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

/**
 * Fonction générant le menu Plugins
 */
include("menu_plugins.inc.php");
	
/*******************************************************************
 *
 *			Construction du menu horizontal de la page d'accueil 
 *			pour le profil administrateur
 *
 *******************************************************************/
	
	
	if ($_SESSION['statut'] == "administrateur") {

		$menus = null;
		// Choix Initialisation
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Initialisation</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/changement_d_annee.php" '.insert_confirm_abandon().'>Changement d\'année</a></li>'."\n";
		if (LDAPServer::is_setup()) {
			$menus .= '     <li><a href="'.$gepiPath.'/init_scribe_ng/index.php" '.insert_confirm_abandon().'>Init.Ldap Scribe</a></li>'."\n";
			$menus .= '     <li><a ';
			if($is_lcs_plugin=='yes') {
				$menus .= 'style="font-weight:bold" ';
			}
			$menus .= 'href="'.$gepiPath.'/init_lcs/index.php" '.insert_confirm_abandon().'>Init.Ldap LCS</a></li>'."\n";
		}
		$menus .= '     <li><a href="'.$gepiPath.'/init_csv/index.php" '.insert_confirm_abandon().'>Initialisation csv</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/init_xml2/index.php" '.insert_confirm_abandon().'>Initialisation xml</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/modify_impression.php" '.insert_confirm_abandon().'>Fiches bienvenue</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";

		// Choix Paramètres
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Paramètres</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_gen.php" '.insert_confirm_abandon().'>Config. générale</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/mod_serveur/test_serveur.php" '.insert_confirm_abandon().'>Config. serveur</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/droits_acces.php" '.insert_confirm_abandon().' title="Cette page permet de définir à quelles informations, à quels modules de Gepi,... les différents statuts (administrateur, scolarité, cpe, professeur, secours, élève, responsable, autre) ont droit d\'accéder.">Droits d\'accès</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/options_connect.php" '.insert_confirm_abandon().'>Options connexions</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/config_prefs.php" '.insert_confirm_abandon().' title="Paramétrage de l\'interface simplifiée des professeurs et interfaces et dispositifs complémentaires pour les personnels (pas seulement professeurs).">Interface Profs</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_couleurs.php" '.insert_confirm_abandon().'>Couleurs</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_ordre_item.php" '.insert_confirm_abandon().'>Ordre des menus</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/modify_impression.php" '.insert_confirm_abandon().'>Fiches Bienvenue</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";

		// Choix Maintenance
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Maintenance</a>'."\n";
		$menus .= '  <ul class="niveau2">'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/accueil_sauve.php" '.insert_confirm_abandon().'>Sauvegardes</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/maj.php" '.insert_confirm_abandon().'>Mise à jour de la base</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/clean_tables.php" '.insert_confirm_abandon().'>Nettoyage des tables</a></li>'."\n";
		if(!getSettingAOui('gepi_en_production')) {
			$menus .= '    <li><a href="'.$gepiPath.'/gestion/efface_base.php" '.insert_confirm_abandon().'>Effacer la base</a></li>'."\n";
		}
		else {
			$menus .= '    <li><span title="Effacer la base : Choix désactivé sur un Gepi en production.
                           Votre Gepi est paramétré comme un Gepi en production :
                           On ne vide normalement pas la base sur un Gepi en production.
                           Si votre Gepi est un Gepi de test, vous pouvez modifier ce
                           paramétrage dans
                                Gestion générale/Configuration générale.">Effacer la base</span></li>'."\n";
		}
		$menus .= '    <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php#purge" '.insert_confirm_abandon().'>Effacer les photos</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/gestion_temp_dir.php" '.insert_confirm_abandon().'>Dossiers temp.</a></li>'."\n";
		$menus .= '</ul>'."\n";
		$menus .= '</li>'."\n";

		// Choix Données
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Données</a>'."\n";
		$menus .= '  <ul class="niveau2">'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/eleves/recherche.php" '.insert_confirm_abandon().' title="Effectuer une recherche sur une personne (élève, responsable ou personnel)">Rechercher</a></li>'."\n";
		$menus .= '        <li><a href="'.$gepiPath.'/matieres/index.php" '.insert_confirm_abandon().'>Matières</a></li>'."\n";

		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/utilisateurs/index.php" '.insert_confirm_abandon().' title="Les comptes d\'utilisateurs permettent de se connecter dans Gepi">Utilisateurs</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/index.php?mode=personnels" '.insert_confirm_abandon().'>Comptes Personnels</a></li>'."\n";
		if (getSettingValue("statuts_prives") == "y") {
			$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/creer_statut.php" '.insert_confirm_abandon().'>Statuts personnalisés</a></li>'."\n";
		}
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_responsable.php" '.insert_confirm_abandon().' title="Consulter, rechercher, modifier des comptes d\'utilisateurs responsables/parents.">Comptes Resp.légaux</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_eleve.php" '.insert_confirm_abandon().' title="Consulter, rechercher, modifier des comptes d\'utilisateurs élèves.">Comptes Elèves</a></li>'."\n";
		if(getSettingAOui('use_ent') || $gepiSettings['auth_sso'] == 'cas') {
			$temp_afficher_liaison_ent=getSettingValue('afficher_liaison_ent');
			if($temp_afficher_liaison_ent!="netcollege") {
				$menus .= '                <li><a href="'.$gepiPath.'/mod_sso_table/index.php" '.insert_confirm_abandon().'>Correspondances identifiants SSO</a></li>'."\n";
			}

			if($temp_afficher_liaison_ent!="") {
				$menus .= '                <li><a href="'.$gepiPath.'/mod_ent/index.php" '.insert_confirm_abandon().'>Liaison ENT</a></li>'."\n";
			}
		}
		$menus .= '                <li><a href="'.$gepiPath.'/gestion/modify_impression.php" '.insert_confirm_abandon().'>Fiches bienvenue</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/index.php?mode=MonCompteAfficheInfo" '.insert_confirm_abandon().' title="Vous pouvez faire apparaître des informations spécifique à chaque statut dans la rubrique Gérer mon compte de l\'utilisateur.">Infos par statut</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		// Elèves
		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/eleves/index.php" '.insert_confirm_abandon().'>Elèves</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/index.php" '.insert_confirm_abandon().'>Gestion des élèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import.php" '.insert_confirm_abandon().'>Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/visu_eleve.php" '.insert_confirm_abandon().'>Consult.fiches élèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Listes élèves</a></li>'."\n";
		// Les administrateurs n'ont pas ce droit:
		//$menus .= '                <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Impression PDF listes</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Export CSV listes</a></li>'."\n";

		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_eleve.php" '.insert_confirm_abandon().' title="Consulter, rechercher, modifier des comptes d\'utilisateurs élèves.">Comptes Elèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php#gestion_fichiers" '.insert_confirm_abandon().'>Trombinoscopes</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		// Responsables
		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/responsables/index.php" '.insert_confirm_abandon().'>Resp. légaux</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/index.php" '.insert_confirm_abandon().'>Gestion resp.légaux</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import.php" '.insert_confirm_abandon().'>Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_responsable.php" '.insert_confirm_abandon().' title="Consulter, rechercher, modifier des comptes d\'utilisateurs responsables/parents.">Comptes Resp.légaux</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		// Classes
		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/classes/index.php" '.insert_confirm_abandon().' title="Gestion des classes, périodes, enseignements associés,...">Classes</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li class="plus"><a href="'.$gepiPath.'/classes/index.php" '.insert_confirm_abandon().' title="Gestion des classes, périodes, enseignements associés,...">Gestion des classes</a>'."\n";
		$menus .= '                    <ul class="niveau4">'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/classes_param.php" '.insert_confirm_abandon().' title="Modification des paramètres des classes par lots de classes.">Paramétrage par lots</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/init_xml2/init_alternatif.php?cat=classes" '.insert_confirm_abandon().' title="Création d\'enseignements par lots.">Créations par lots</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/groupes/repartition_ele_grp.php" '.insert_confirm_abandon().' title="Répartition d\'élèves entre plusieurs enseignements/groupes.">Répartition entre groupes</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/prof_suivi.php" '.insert_confirm_abandon().' title="Définition des '.getSettingValue('gepi_prof_suivi').' des élèves dans les différentes classes.">Param.'.getSettingValue('gepi_prof_suivi').'</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/cpe_resp.php" '.insert_confirm_abandon().' title="Définition du CPE responsable des élèves de telle ou telle classe.'.getSettingValue('gepi_prof_suivi').' des élèves dans les différentes classes.">Param.CPE resp</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/scol_resp.php" '.insert_confirm_abandon().' title="Définition des classes visibles par défaut par les différents comptes de statut scolarité.">Param.SCOL resp</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/acces_appreciations.php" '.insert_confirm_abandon().' title="Définition des appréciations et avis des conseils de classe visibles à telle date ou ouvertes manuellement en consultation selon ce qui a été paramétré dans la Configuration générale.">Accès appréciations</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/groupes/check_enseignements.php" '.insert_confirm_abandon().' title="Vérification des coefficients, visibilité,... des enseignements de l\'ensemble des classes.">Vérifications</a></li>'."\n";

		if(getSettingAOui("active_carnets_notes")) {
			$menus .= '                        <li><a href="'.$gepiPath.'/cahier_notes_admin/creation_conteneurs_par_lots.php" '.insert_confirm_abandon().' title="Création de '.getSettingValue("gepi_denom_boite").' par lots dans les carnets de notes de certains enseignements (par exemple pour Oral/Ecrit, Travaux en classe/Travaux à la maison,...).">Création de '.getSettingValue("gepi_denom_boite").'</a></li>'."\n";
		}

		$menus .= '                    </ul>'."\n";
		$menus .= '                </li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php" '.insert_confirm_abandon().' title="Consulter les équipes pédagogiques.
Les listes de professeurs associés à telle classe,
pour tel enseignement, avec les effectifs par période
seront affichés (et exportables en CSV).">Equipes pédago</a></li>'."\n";

		$menus .= '                <li><a href="'.$gepiPath.'/statistiques/classes_effectifs.php"'.insert_confirm_abandon().' title="Effectifs par classe et périodes.">Effectifs</a></li>'."\n";

		$menus .= '                <li><a href="'.$gepiPath.'/mef/admin_mef.php" '.insert_confirm_abandon().'>MEF</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/gestion/admin_nomenclatures.php" '.insert_confirm_abandon().'>Nomenclatures</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/classes/dates_classes.php"'.insert_confirm_abandon().' title="Faire apparaître des événements en page d\'accueil pour telle ou telle classe de telle à telle date,...
Vous pouvez notamment faire apparaître un tableau des dates de conseils de classe.">Date événements</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/mef/admin_mef.php" '.insert_confirm_abandon().' title="Gestion des Modules élémentaires de formation">MEF</a></li>'."\n";
		$menus .= '        <li><a href="'.$gepiPath.'/gestion/admin_nomenclatures.php" '.insert_confirm_abandon().'>Nomenclatures</a></li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/aid/index.php" '.insert_confirm_abandon().'>AID</a></li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/etablissements/index.php" '.insert_confirm_abandon().'>Etablissements</a></li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/statistiques/index.php" '.insert_confirm_abandon().'>Statistiques</a></li>'."\n";

		if(!getSettingAOui('gepi_en_production')) {
			$menus .= '        <li><a href="'.$gepiPath.'/gestion/gestion_base_test.php" '.insert_confirm_abandon().'>Données de tests</a></li>'."\n";
		}
		else {
			$menus .= '    <li><span title="Données de test : Choix désactivé sur un Gepi en production.
                               Les données de test ajoutent des enregistrements dans la
                               base pour pouvoir tester Gepi sans remplir soi-même la base.
                           
                               Votre Gepi est paramétré comme un Gepi en production :
                               Normalement, on n ajoute pas dans la base des données
                               de test sur un Gepi en production.
                               Si votre Gepi est un Gepi de test, vous pouvez modifier ce
                               paramétrage dans
                                    Gestion générale/Configuration générale.">Données de tests</span></li>'."\n";
		}
		$menus .= '  </ul>'."\n";
		$menus .= '</li>'."\n";

		// Choix Modules
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Modules</a>'."\n";
		$menus .= '<ul class="niveau2">'."\n";
		$menus .= '  <li class="plus"><a href="'.$gepiPath.'/accueil_modules.php" '.insert_confirm_abandon().'>Paramétrages</a>'."\n";
		$menus .= '    <ul class="niveau3">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/index.php" '.insert_confirm_abandon().'>Cahier de textes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_notes_admin/index.php" '.insert_confirm_abandon().'>Carnets de notes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/index_admin.php" '.insert_confirm_abandon().'>Bulletins</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_absences/admin/index.php" '.insert_confirm_abandon().'>Absences</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_abs2/admin/index.php" '.insert_confirm_abandon().'>Absences 2</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/edt_organisation/edt.php" '.insert_confirm_abandon().'>Emplois du temps</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/edt/index_admin.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php" '.insert_confirm_abandon().'>Trombinoscopes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_notanet/notanet_admin.php" '.insert_confirm_abandon().'>Notanet/Brevet</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_inscription/inscription_admin.php" '.insert_confirm_abandon().'>Inscription</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/rss_cdt_admin.php" '.insert_confirm_abandon().'>Flux RSS</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/utilisateurs/creer_statut_admin.php" '.insert_confirm_abandon().'>Statuts perso.</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_annees_anterieures/admin.php" '.insert_confirm_abandon().'>Années antérieures</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_discipline/discipline_admin.php" '.insert_confirm_abandon().'>Discipline</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ooo/ooo_admin.php" '.insert_confirm_abandon().'>Modèles openDocument</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ects/ects_admin.php" '.insert_confirm_abandon().'>Saisie ECTS</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_genese_classes/admin.php" '.insert_confirm_abandon().'>Genèse des classes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_epreuve_blanche/admin.php" '.insert_confirm_abandon().'>Epreuves blanches</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_examen_blanc/admin.php" '.insert_confirm_abandon().'>Examens blancs</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_gest_aid/admin.php" '.insert_confirm_abandon().'>Gestionnaires AID</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_alerte/admin.php" '.insert_confirm_abandon().'>Dispositif d\'alerte</a></li>'."\n";
		$menus .= '    </ul>'."\n";		
		$menus .= '  </li>'."\n";

		if (getSettingValue("active_module_absence")=='2') {
			// Admin n'a pas le droit de consultation des absences en mod_abs2, mais il l'a en mod_absences (1)
			//$menus .= '  <li><a href="'.$gepiPath.'/mod_abs2/index.php" '.insert_confirm_abandon().'>Absences</a></li>'."\n";
		}
		elseif (getSettingValue("active_module_absence")=='y') {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_absences/gestion/voir_absences_viescolaire.php" '.insert_confirm_abandon().'>Absences</a></li>'."\n";
		}

		if(getSettingAOui('active_module_trombinoscopes')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php" '.insert_confirm_abandon().'>Trombinoscopes</a></li>'."\n";
		}

		if((getSettingAOui('autorise_edt_tous'))||(getSettingAOui('autorise_edt_admin'))||(getSettingAOui('autorise_edt_eleve'))) {
			$menus .= '  <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php" '.insert_confirm_abandon().'>Emplois du temps</a></li>'."\n";
		}

		if(getSettingAOui('active_edt_ical')) {
			$menus .= '  <li><a href="'.$gepiPath.'/edt/index.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
		}

		$menus .= '  <li class="plus"><a href="#">Bulletins</a>'."\n";
		$menus .= '    <ul class="niveau3">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/autorisation_exceptionnelle_saisie_app.php" '.insert_confirm_abandon().'>Droits saisie profs</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/classes/acces_appreciations.php" '.insert_confirm_abandon().'>Droits accès élèves</a></li>'."\n";

		if(getSettingValue('type_bulletin_par_defaut')=="pdf") {
			$menus .= '      <li><a href="'.$gepiPath.'/bulletin/param_bull_pdf.php" '.insert_confirm_abandon().'>Param. impression</a></li>'."\n";
		}
		else {
			$menus .= '      <li><a href="'.$gepiPath.'/bulletin/param_bull.php" '.insert_confirm_abandon().'>Param. impression</a></li>'."\n";
		}

		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/bull_index.php" '.insert_confirm_abandon().'>Impression</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/statistiques/index.php" '.insert_confirm_abandon().'>Extractions stats</a></li>'."\n";
		$gepi_denom_mention=getSettingValue('gepi_denom_mention');
		if($gepi_denom_mention=='') {$gepi_denom_mention="mention";}
		$menus .= '      <li><a href="'.$gepiPath.'/saisie/saisie_mentions.php" '.insert_confirm_abandon().'>'.ucfirst($gepi_denom_mention).'s</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/modify_impression.php" '.insert_confirm_abandon().'>Fiches Bienvenue</a></li>'."\n";
		$menus .= '    </ul>'."\n";		
		$menus .= '  </li>'."\n";
		if(getSettingAOui('active_notanet')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_notanet/index.php" '.insert_confirm_abandon().'>Notanet/Brevet</a></li>'."\n";
		}
		if(getSettingAOui('active_annees_anterieures')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_annees_anterieures/index.php" '.insert_confirm_abandon().'>Années antérieures</a></li>'."\n";
		}
		$menus .= '  <li><a href="'.$gepiPath.'/messagerie/index.php" '.insert_confirm_abandon().' title="Le Panneau d\'affichage permet de faire apparaître en page d\'accueil des messages destinés à certains utilisateurs ou catégories d\'utilisateurs à compter d\'une date à choisir et pour une durée à choisir également.">Panneau d\'affichage</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/classes/dates_classes.php"'.insert_confirm_abandon().' title="Faire apparaître des événements en page d\'accueil pour telle ou telle classe de telle à telle date,...
Vous pouvez notamment faire apparaître un tableau des dates de conseils de classe.">Date événements</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_ooo/index.php" '.insert_confirm_abandon().'>Modèles openDocument</a></li>'."\n";
		if(getSettingAOui('active_mod_discipline')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_discipline/index.php" '.insert_confirm_abandon().'>Discipline/Sanctions</a></li>'."\n";
		}
		if(getSettingAOui('active_mod_epreuve_blanche')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_epreuve_blanche/index.php" '.insert_confirm_abandon().'>Epreuves blanches</a></li>'."\n";
		}
		if(getSettingAOui('active_mod_examen_blanc')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_examen_blanc/index.php" '.insert_confirm_abandon().'>Examens blancs</a></li>'."\n";
		}
		if(getSettingAOui('active_cahiers_texte')) {
			$menus .= '  <li><a href="'.$gepiPath.'/cahier_texte_admin/visa_ct.php" '.insert_confirm_abandon().'>Visa c. de textes</a></li>'."\n";
		}


		if(getSettingAOui('active_inscription')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_inscription/inscription_config.php" '.insert_confirm_abandon().'>Inscriptions</a></li>'."\n";
		}
		if(getSettingAOui('active_mod_genese_classes')) {
			$menus .= '  <li><a href="'.$gepiPath.'/mod_genese_classes/index.php" '.insert_confirm_abandon().'>Genèse des classes</a></li>'."\n";
		}
		$menus .= '</ul>'."\n";	
		$menus .= '</li>'."\n";

		// Choix Plugins
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Plugins</a>'."\n";
		$menus .= '    <ul class="niveau2">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_plugins/index.php" '.insert_confirm_abandon().'>Gestion des plugins</a></li>'."\n";
		$menus.='		'.menu_plugins();
		$menus .= '    </ul>'."\n";		
		$menus .= '</li>'."\n";	

		// Choix Sécurité
		$menus .= '  <li class="li_inline"><a href="'.$gepiPath.'/gestion/index.php" '.insert_confirm_abandon().'>Sécurité</a>'."\n";
		$menus .= '    <ul class="niveau2">'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/gestion_connect.php" '.insert_confirm_abandon().'>Connexions</a></li>'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_panel.php" '.insert_confirm_abandon().'>Alertes</a></li>'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_policy.php" '.insert_confirm_abandon().'>Politique de sécurité</a></li>'."\n";
		$menus .= '    </ul>'."\n";
		$menus .= '  </li>'."\n";

		$tbs_menu_admin[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php" '.insert_confirm_abandon().'>Accueil</a></li>'."\n");
		$tbs_menu_admin[]=array("li"=> $menus);	
	}

?>
