<?php

/**
 * Fichier qui permet de construire la barre de menu eleve des pages utilisant un gabarit
 * 
 * 
 * Variables envoyées au gabarit
 * - $tbs_menu_admin : liens de la barre de menu 
 *
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see getSettingValue()
 * @see insert_confirm_abandon()
 * @todo Réécrire la barre administrateur, le principe des gabarits, c'est d'envoyer des variables aux gabarits, 
 * pas d'écrire du code html dans le constructeur
 */

/* This file is part of GEPI.
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
 * Fichier qui permet de construire la barre de menu responsable
 *
 */
 
 
 
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

// Fonction générant le menu Plugins
include("menu_plugins.inc.php");
$barre_plugin=menu_plugins();
if ($barre_plugin!="") {
	$barre_plugin = "<li class='li_inline'><a href=\"\">Plugins</a>"."\n"
					."	<ul class='niveau2'>\n"
					.$barre_plugin
					."	</ul>\n"
					."</li>\n";
}
// fin plugins

/*******************************************************************
 *
 *			Construction du menu horizontal de la page d'accueil 
 *			pour le profil eleve
 *
 *******************************************************************/

	
	if ($_SESSION['statut'] == "eleve") {

		$menus = null;

		// EDT
		if (getSettingValue("autorise_edt_eleve")=="yes"){
			// on propose l'edt d'un élève (le premier du tableau), les autres enfants seront disponibles dans la page de l'edt.
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/edt_eleve.php?login_edt='.$_SESSION['login'].'"'.insert_confirm_abandon().' title="Cet outil permet la consultation de votre emploi du temps.">&nbsp;EDT</a></li>'."\n";
		}

		if((getSettingAOui('active_edt_ical'))&&(getSettingAOui('EdtIcalEleve'))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
		}

		// Cahiers de textes
		if((getSettingAOui("active_cahiers_texte"))&&(getSettingAOui("GepiAccesCahierTexteEleve"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte/consultation.php"'.insert_confirm_abandon().' title="Permet de consulter les compte-rendus de séance et vos devoirs à faire.">&nbsp;Cahier de textes</a></li>'."\n";
		}

		// Carnet de notes
		if((getSettingAOui("active_carnets_notes"))&&(getSettingAOui("GepiAccesReleveEleve"))) {
			if(getSettingAOui('GepiAccesEvalCumulEleve')) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().' title="Permet de consulter vos relevés de notes.">&nbsp;Carnet de notes</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().'>Relevé de notes</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_cc_elv.php"'.insert_confirm_abandon().'>Notes cumulées</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			else {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().' title="Permet de consulter vos relevés de notes.">&nbsp;Carnet de notes</a></li>'."\n";
			}
		}

		// Bulletins
		if(getSettingAOui("active_bulletins")) {
			if((getSettingAOui("GepiAccesBulletinSimpleEleve"))&&(getSettingAOui("GepiAccesGraphEleve"))) {
				$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().' title="Permet de consulter vos bulletins simplifiés.">&nbsp;Bulletins simplifiés</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().' title="Permet de visualiser sous forme graphique vos résultats.">&nbsp;Visualis.graphique</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			elseif(getSettingAOui("GepiAccesBulletinSimpleEleve")) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().' title="Permet de consulter vos bulletins simplifiés.">&nbsp;Bulletins</a></li>'."\n";
			}
			elseif(getSettingAOui("GepiAccesGraphEleve")) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().' title="Permet de visualiser sous forme graphique vos résultats.">&nbsp;Visu.graph</a></li>'."\n";
			}
		}

		// Equipe pédagogique
		if(getSettingAOui("GepiAccesEquipePedaEleve")) {
		   $menus .= '<li class="li_inline"><a href="'.$gepiPath.'/groupes/visu_profs_eleve.php"'.insert_confirm_abandon().' title="Permet de consulter l\'équipe pédagogique de votre classe.">&nbsp;Éq.pédago</a></li>'."\n";
		}

		// Discipline
		if((getSettingAOui("active_mod_discipline"))&&(getSettingAOui("visuEleDisc"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/visu_disc.php"'.insert_confirm_abandon().' title="Incidents vous concernant.">&nbsp;Discipline</a></li>'."\n";
		}

		if(getSettingAOui('AAEleve')) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_annees_anterieures/consultation_annee_anterieure.php"'.insert_confirm_abandon().' title="Consulter les données d\'années antérieures (bulletins simplifiés,...) vous concernant.">&nbsp;Années antérieures</a></li>'."\n";
		}

		$menus .= $barre_plugin;

		$tbs_menu_eleve[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_eleve[]=array("li"=> $menus);

	}
	//print_r($tbs_menu_eleve);
?>
