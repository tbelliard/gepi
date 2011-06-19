<?php

/*
 * $Id: header_barre_admin_template.php $
 *
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
 *
 * Fichier qui permet de construire la barre de menu professeur
 *
 */
 
 
/* ---------Variables envoyées au gabarit
*	----- tableaux -----
* $tbs_menu_admin										liens se la barre de menu prof
*				-> li
*

$TBS->MergeBlock('tbs_menu_prof',$tbs_menu_prof) ;

unset($tbs_menu_prof);
*/
 
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}


	if ($_SESSION['statut'] == "administrateur") {
		$tbs_menu_admin[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>');	
		$menus = null;
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Paramètres</a>';
		$menus .= '<ul class="niveau2">';
		$menus .= '          <li><a href="'.$gepiPath.'/mod_serveur/test_serveur.php">Votre serveur</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/param_gen.php">Votre établissement</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/droits_acces.php">Droits d\'accès</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/options_connect.php">Options connexions</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/modify_impression.php">Fiches bienvenue</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/config_prefs.php">Interface Profs</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/param_couleurs.php">Couleurs</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/param_ordre_item.php">Ordre des menus</a>';
		$menus .= '</ul>';
		$menus .= '</li>';
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Bases de données</a>';
		$menus .= '  <ul class="niveau2">';
		$menus .= '    <li><a href="#">Initialisations</a>';
		$menus .= '      <ul class="niveau3">';
		$menus .= '        <li><a href="'.$gepiPath.'/init_csv/index.php">Initialisation csv</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/inti_xml2/index.php">Initialisation xml</a></li>';
		$menus .= '      </ul>';
		$menus .= '    </li>';
		$menus .= '    <li><a href="'.$gepiPath.'/accueil_admin.php">Administration</a>';
		$menus .= '      <ul class="niveau3">';
		$menus .= '        <li><a href="'.$gepiPath.'/etablissements/index.php">Etablissements</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/matieres/index.php">Matières</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/utilisateurs/index.php">Utilisateurs</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/eleves/index.php">Elèves</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/responsables/index.php">Resp. légaux</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/classes/index.php">Classes</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/aid/index.php">AID</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php#gestion_fichiers">Trombinoscopes</a></li>';
		$menus .= '        <li><a href="'.$gepiPath.'/mef/admin_mef.php">MEF</a></li>';
		$menus .= '      </ul>';		
		$menus .= '    </li>';
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/accueil_sauve.php">Sauvegardes</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/maj.php">Mise à jour</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/utilitaires/clean_tables.php">Nettoyage</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/efface_base.php">Effacer la base</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/efface_photos.php">Effacer les photos</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/gestion_temp_dir.php">Dossiers temp.</a>';
		$menus .= '    <li><a href="'.$gepiPath.'/gestion/gestion_base_test.php">Données de tests</a>';
		$menus .= '  </ul>';
		$menus .= '</li>';

		$menus .= '<li class="li_inline"><a href="#">&nbsp;Modules</a>';
		$menus .= '<ul class="niveau2">';
		$menus .= '  <li><a href="'.$gepiPath.'/accueil_modules.php">Paramétrages</a>';
		$menus .= '    <ul class="niveau3">';
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/index.php">Cahier de textes</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_notes_admin/index.php">Carnets de notes</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_absences/admin/index.php">Absences</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_abs2/admin/index.php">Absences 2</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/edt_organisation/edt.php">Emplois du temps</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes_admin.php">Trombinoscopes</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_notanet/notanet_admin.php">Notanet/Brevet</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_inscription/inscription_admin.php">Inscription</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/cahier_texte_admin/rss_cdt_admin.php">Flux RSS</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/utilisateurs/creer_statut_admin.php">Statuts perso.</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_annees_anterieures/admin.php">Années antérieures</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_discipline/discipline_admin.php">Discipline</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ooo/ooo_admin.php">Modèles OpenOffice</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_ects/ects_admin.php">Saisie ECTS</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_plugins/index.php">Plugins</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_genese_classes/admin.php">Génèse des classes</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_epreuve_blanche/admin.php">Epreuves blanches</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_examen_blanc/admin.php">Examens blancs</a></li>';
		$menus .= '      <li><a href="'.$gepiPath.'/mod_gest_aid/admin.php">Gestionnaires AID</a></li>';
		$menus .= '    </ul>';		
		$menus .= '  </li>';

		$menus .= '  <li><a href="#">Absences</a>';
		$menus .= '  </li>';		
		$menus .= '  <li><a href="#">Trombinoscopes</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Emplois du temps</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Bulletins</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Notanet/Brevet</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Années antérieures</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Panneau d\'affichage</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Modèles OpenOffice</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Discipline/Sanctions</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Génèse des classes</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Epreuves blanches</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Examens blancs</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Visa c. de textes</a>';
		$menus .= '  </li>';
		$menus .= '  <li><a href="#">Génèse des classes</a>';
		$menus .= '  </li>';
		$menus .= '</ul>';	
		$menus .= '</li>';
		$menus .= '<li class="li_inline"><a href="#">&nbsp;Plugins</a>';
		$menus .= '</li>';	
		
		$menus .= '  <li class="li_inline"><a href="'.$gepiPath.'/gestion/index.php">Sécurité</a>';
		$menus .= '    <ul class="niveau2">';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/gestion_connect.php">Connexions</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_panel.php">Alertes</a>';
		$menus .= '          <li><a href="'.$gepiPath.'/gestion/security_policy.php">Politique de sécurité</a>';
		$menus .= '    </ul>';
		$menus .= '  </li>';		
		$tbs_menu_admin[]=array("li"=> $menus);	
	}

?>
