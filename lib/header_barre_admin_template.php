<?php

/**
 * Barre de menu des pages administrateurs utilisant un gabarit
 * 
 * 
 * 
 * $Id: header_barre_admin_template.php 7899 2011-08-22 13:13:49Z crob $
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
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Initialisation</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/changement_d_annee.php">Changement d\'année</a></li>'."\n";
		if (LDAPServer::is_setup()) {
			$menus .= '     <li><a href="'.$gepiPath.'/init_scribe_ng/index.php">Init.Ldap Scribe</a></li>'."\n";
			$menus .= '     <li><a ';
			if($is_lcs_plugin=='yes') {
				$menus .= 'style="font-weight:bold" ';
			}
			$menus .= 'href="'.$gepiPath.'/init_lcs/index.php">Init.Ldap LCS</a></li>'."\n";
		}
		$menus .= '     <li><a href="'.$gepiPath.'/init_csv/index.php">Initialisation csv</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/init_xml2/index.php">Initialisation xml</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/modify_impression.php">Fiches bienvenue</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Paramètres</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_gen.php">Config. générale</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/mod_serveur/test_serveur.php">Config. serveur</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/droits_acces.php">Droits d\'accès</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/options_connect.php">Options connexions</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/config_prefs.php">Interface Profs</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_couleurs.php">Couleurs</a></li>'."\n";
		$menus .= '     <li><a href="'.$gepiPath.'/gestion/param_ordre_item.php">Ordre des menus</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Maintenance</a>'."\n";
		$menus .= '  <ul class="niveau2">'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/accueil_sauve.php">Sauvegardes</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/maj.php">Mise à jour de la base</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/clean_tables.php">Nettoyage de la base</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/efface_base.php">Effacer la base</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/efface_photos.php">Effacer les photos</a></li>'."\n";
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/gestion_temp_dir.php">Dossiers temp.</a></li>'."\n";
		$menus .= '</ul>'."\n";
		$menus .= '</li>'."\n";
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Données</a>'."\n";
		$menus .= '  <ul class="niveau2">'."\n";
		
		$menus .= '        <li><a href="'.$gepiPath.'/matieres/index.php">Matières</a></li>'."\n";
		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/utilisateurs/index.php">Utilisateurs</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/index.php?mode=personnels">Comptes Personnels</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_responsable.php">Comptes Resp.légaux</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_eleve.php">Comptes Elèves</a></li>'."\n";
        $menus .= '                <li><a href="'.$gepiPath.'/mod_sso_table/index.php">Correspondances identifiants SSO</a></li>'."\n";
		$menus .= '            </ul>'."\n";		
		$menus .= '        </li>'."\n";

		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/eleves/index.php">Elèves</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/index.php">Gestion des élèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import.php">Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/visu_eleve.php">Consult.fiches élèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_eleve.php">Comptes Elèves</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php#gestion_fichiers">Trombinoscopes</a></li>'."\n";
		$menus .= '            </ul>'."\n";		
		$menus .= '        </li>'."\n";

		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/responsables/index.php">Resp. légaux</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/index.php">Gestion resp.légaux</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import.php">Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/utilisateurs/edit_responsable.php">Comptes Resp.légaux</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		$menus .= '        <li class="plus"><a href="'.$gepiPath.'/classes/index.php">Classes</a>'."\n";
		$menus .= '            <ul class="niveau3">'."\n";
		$menus .= '                <li class="plus"><a href="'.$gepiPath.'/classes/index.php">Gestion des classes</a>'."\n";
		$menus .= '                    <ul class="niveau4">'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/classes_param.php">Paramétrage par lots</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/prof_suivi.php">Param.'.getSettingValue('gepi_prof_suivi').'</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/cpe_resp.php">Param.CPE resp</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/scol_resp.php">Param.SCOL resp</a></li>'."\n";
		$menus .= '                        <li><a href="'.$gepiPath.'/classes/acces_appreciations.php">Accès appréciations</a></li>'."\n";
		$menus .= '                    </ul>'."\n";
		$menus .= '                </li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php">Equipes pédago</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/mef/admin_mef.php">MEF</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '        </li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/aid/index.php">AID</a></li>'."\n";

		$menus .= '        <li><a href="'.$gepiPath.'/etablissements/index.php">Etablissements</a></li>'."\n";
		$menus .= '        <li><a href="'.$gepiPath.'/gestion/gestion_base_test.php">Données de tests</a></li>'."\n";
		$menus .= '  </ul>'."\n";
		$menus .= '</li>'."\n";
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Modules</a>'."\n";
		$menus .= '<ul class="niveau2">'."\n";
		$menus .= '  <li class="plus"><a href="'.$gepiPath.'/accueil_modules.php">Paramétrages</a>'."\n";
		$menus .= '    <ul class="niveau3">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/index.php">Cahier de textes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_notes_admin/index.php">Carnets de notes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_absences/admin/index.php">Absences</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_abs2/admin/index.php">Absences 2</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/edt_organisation/edt.php">Emplois du temps</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php">Trombinoscopes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_notanet/notanet_admin.php">Notanet/Brevet</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_inscription/inscription_admin.php">Inscription</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/rss_cdt_admin.php">Flux RSS</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/utilisateurs/creer_statut_admin.php">Statuts perso.</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_annees_anterieures/admin.php">Années antérieures</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_discipline/discipline_admin.php">Discipline</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ooo/ooo_admin.php">Modèles OpenOffice</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ects/ects_admin.php">Saisie ECTS</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_genese_classes/admin.php">Génèse des classes</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_epreuve_blanche/admin.php">Epreuves blanches</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_examen_blanc/admin.php">Examens blancs</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_gest_aid/admin.php">Gestionnaires AID</a></li>'."\n";
		$menus .= '    </ul>'."\n";		
		$menus .= '  </li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_absences/gestion/voir_absences_viescolaire.php">Absences</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php">Trombinoscopes</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php">Emplois du temps</a></li>'."\n";
		$menus .= '  <li class="plus"><a href="#">Bulletins</a>'."\n";
		$menus .= '    <ul class="niveau3">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/autorisation_exceptionnelle_saisie_app.php">Droits saisie profs</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/classes/acces_appreciations.php">Droits accès élèves</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/param_bull.php">Param. impression</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/bulletin/bull_index.php">Impression</a></li>'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/statistiques/index.php">Extractions stats</a></li>'."\n";
		$gepi_denom_mention=getSettingValue('gepi_denom_mention');
		if($gepi_denom_mention=='') {$gepi_denom_mention="mention";}
		$menus .= '      <li><a href="'.$gepiPath.'/saisie/saisie_mentions.php">'.ucfirst($gepi_denom_mention).'s</a></li>'."\n";
		$menus .= '    </ul>'."\n";		
		$menus .= '  </li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_notanet/index.php">Notanet/Brevet</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_annees_anterieures/index.php">Années antérieures</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/messagerie/index.php">Panneau d\'affichage</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_ooo/index.php">Modèles OpenOffice</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_discipline/index.php">Discipline/Sanctions</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_epreuve_blanche/index.php">Epreuves blanches</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_examen_blanc/index.php">Examens blancs</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/cahier_texte_admin/visa_ct.php">Visa c. de textes</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_inscription/inscription_config.php">Inscriptions</a></li>'."\n";
		$menus .= '  <li><a href="'.$gepiPath.'/mod_genese_classes/index.php">Génèse des classes</a></li>'."\n";
		$menus .= '</ul>'."\n";	
		$menus .= '</li>'."\n";
		
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Plugins</a>'."\n";
		$menus .= '    <ul class="niveau2">'."\n";
		$menus .= '      <li><a href="'.$gepiPath.'/mod_plugins/index.php">Gestion des plugins</a></li>'."\n";
		$menus.='		'.menu_plugins();
		$menus .= '    </ul>'."\n";		
		$menus .= '</li>'."\n";	
		
		$menus .= '  <li class="li_inline"><a href="'.$gepiPath.'/gestion/index.php">Sécurité</a>'."\n";
		$menus .= '    <ul class="niveau2">'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/gestion_connect.php">Connexions</a></li>'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_panel.php">Alertes</a></li>'."\n";
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_policy.php">Politique de sécurité</a></li>'."\n";
		$menus .= '    </ul>'."\n";
		$menus .= '  </li>'."\n";

		$tbs_menu_admin[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>'."\n");
		$tbs_menu_admin[]=array("li"=> $menus);	
	}

?>
