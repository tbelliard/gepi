<?php

/**
 * Fichier qui permet de construire la barre de menu responsable des pages utilisant un gabarit
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
 *			pour le profil responsable
 *
 *******************************************************************/

	
	if ($_SESSION['statut'] == "responsable") {

		$tab_tmp_ele = get_enfants_from_resp_login($_SESSION['login'],'', 'y');

		$menus = null;

		// EDT
		if (getSettingValue("autorise_edt_eleve")=="yes"){
			// on propose l'edt d'un élève (le premier du tableau), les autres enfants seront disponibles dans la page de l'edt.
			if(getSettingValue('edt_version_defaut')=="2") {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index2.php"'.insert_confirm_abandon().' title="Cet outil permet la consultation de l\'emploi du temps de votre enfant.">&nbsp;EDT</a></li>'."\n";
			}
			else {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/edt_eleve.php?login_edt='.$tab_tmp_ele[0].'"'.insert_confirm_abandon().' title="Cet outil permet la consultation de l\'emploi du temps de votre enfant.">&nbsp;EDT</a></li>'."\n";
			}
		}

		if((getSettingAOui('active_edt_ical'))&&(getSettingAOui('EdtIcalResponsable'))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
		}

		// Cahiers de textes
		if((getSettingAOui("active_cahiers_texte"))&&(getSettingAOui("GepiAccesCahierTexteParent"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte/consultation.php"'.insert_confirm_abandon().' title="Permet de consulter les compte-rendus de séance et les devoirs à faire pour les '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Cahier de textes</a></li>'."\n";
		}

		// Carnet de notes
		if((getSettingAOui("active_carnets_notes"))&&(getSettingAOui("GepiAccesReleveParent"))) {
			if(getSettingAOui('GepiAccesEvalCumulEleve')) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().' title="Permet de consulter les relevés de notes des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Carnet de notes</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().'>Relevé de notes</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_cc_elv.php"'.insert_confirm_abandon().'>Notes cumulées</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			else {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_ter.php"'.insert_confirm_abandon().' title="Permet de consulter les relevés de notes des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Carnet de notes</a></li>'."\n";
			}
		}

		// Bulletins
		if(getSettingAOui("active_bulletins")) {
			if((getSettingAOui("GepiAccesBulletinSimpleParent"))&&(getSettingAOui("GepiAccesGraphParent"))) {
				$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().' title="Permet de consulter les bulletins simplifiés des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Bulletins simplifiés</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().' title="Permet de visualiser sous forme graphique les résultats des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Visualis.graphique</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			elseif(getSettingAOui("GepiAccesBulletinSimpleParent")) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().' title="Permet de consulter les bulletins simplifiés des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Bulletins</a></li>'."\n";
			}
			elseif(getSettingAOui("GepiAccesGraphParent")) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/visualisation/affiche_eleve.php"'.insert_confirm_abandon().' title="Permet de visualiser sous forme graphique les résultats des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Visu.graph</a></li>'."\n";
			}
		}

		// Equipe pédagogique
		if(getSettingAOui("GepiAccesEquipePedaParent")) {
		   $menus .= '<li class="li_inline"><a href="'.$gepiPath.'/groupes/visu_profs_eleve.php"'.insert_confirm_abandon().' title="Permet de consulter l\'équipe pédagogique des '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Éq.pédago</a></li>'."\n";
		}

		// Absences
		if((getSettingValue("active_module_absence") == '2')&&(getSettingAOui("active_absences_parents"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs2/bilan_parent.php"'.insert_confirm_abandon().' title="Permet de suivre les absences et les retards des élèves '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Absences</a></li>'."\n";
		}
		elseif((getSettingValue("active_module_absence") == 'y')&&(getSettingAOui("active_absences_parents"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_absences/absences.php"'.insert_confirm_abandon().' title="Permet de suivre les absences et les retards des élèves '.getSettingValue('denomination_eleves').' dont vous êtes le '.getSettingValue('denomination_responsable').'.">&nbsp;Absences</a></li>'."\n";
		}

		// Discipline
		if((getSettingAOui("active_mod_discipline"))&&(getSettingAOui("visuRespDisc"))) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/visu_disc.php"'.insert_confirm_abandon().' title="Incidents concernant les élèves/enfants dont vous êtes '.getSettingValue('denomination_responsable').'.">&nbsp;Discipline</a></li>'."\n";
		}

		if(getSettingAOui('AAResponsable')) {
			// Est-ce que le responsable est bien associé à un élève?
			$sql="SELECT 1=1 FROM resp_pers rp, responsables2 r, eleves e
			WHERE rp.pers_id=r.pers_id AND
			r.ele_id=e.ele_id AND
			rp.login='".$_SESSION['login']."';";
			$resultat = mysqli_query($mysqli, $sql);  
			$nb_lignes = $resultat->num_rows;
			$resultat->close();
			if($nb_lignes>0) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_annees_anterieures/consultation_annee_anterieure.php"'.insert_confirm_abandon().' title="Consulter les données d\'années antérieures (bulletins simplifiés,...) concernant les élèves/enfants dont vous êtes '.getSettingValue('denomination_responsable').'.">&nbsp;Années antérieures</a></li>'."\n";
			}
		}

		$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/responsables/infos_parent.php"'.insert_confirm_abandon().' title="Permet de consulter les informations vous concernant (coordonnées téléphoniques, adresse,...) dont dispose l\'établissement.">&nbsp;Infos.personnelles</a></li>'."\n";

		$menus .= $barre_plugin;

		$tbs_menu_responsable[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_responsable[]=array("li"=> $menus);

	}
	//print_r($tbs_menu_responsable);
?>
