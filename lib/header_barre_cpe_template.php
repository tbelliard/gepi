<?php

/**
 * Fichier qui permet de construire la barre de menu cpe des pages utilisant un gabarit
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
 * Fichier qui permet de construire la barre de menu professeur
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
 *			pour le profil administrateur
 *
 *******************************************************************/


	if ($_SESSION['statut'] == "cpe") {

		$tmp_liste_classes_cpe=array();
		$sql="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe;"; 

		$res_tmp_liste_classes_cpe = mysqli_query($mysqli, $sql);
		if($res_tmp_liste_classes_cpe->num_rows > 0) {
			$tmp_cpt_classes_cpe=0;
			while($lig_tmp_liste_classes_cpe = $res_tmp_liste_classes_cpe->fetch_object() ){
				$tmp_liste_classes_cpe[$tmp_cpt_classes_cpe]=array();
				$tmp_liste_classes_cpe[$tmp_cpt_classes_cpe]['id']=$lig_tmp_liste_classes_cpe->id;
				$tmp_liste_classes_cpe[$tmp_cpt_classes_cpe]['classe']=$lig_tmp_liste_classes_cpe->classe;
				$tmp_liste_classes_cpe[$tmp_cpt_classes_cpe]['nom_complet']=$lig_tmp_liste_classes_cpe->nom_complet;
				$tmp_cpt_classes_cpe++;
			}
		}

		$acces_saisie_engagement="n";
		if(getSettingAOui('active_mod_engagements')) {
			$tab_engagements_avec_droit_saisie=get_tab_engagements_droit_saisie_tel_user($_SESSION['login']);
			if(count($tab_engagements_avec_droit_saisie['indice'])>0) {
				$acces_saisie_engagement="y";
			}
		}

		$menus = null;

		if (getSettingValue("active_module_absence") == 'y') {
			$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Absences</a>'."\n";
			$menus .= '   <ul class="niveau2">'."\n";
			$menus .= '     <li><a href="'.$gepiPath.'/mod_absences/gestion/gestion_absences.php"'.insert_confirm_abandon().'>Gestion absences</a></li>'."\n";
			$menus .= '     <li><a href="'.$gepiPath.'/mod_absences/gestion/voir_absences_viescolaire.php"'.insert_confirm_abandon().'>Visu. absences</a></li>'."\n";
			if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
				$menus .= '     <li><a href="'.$gepiPath.'/absences/index.php"'.insert_confirm_abandon().'>Absences bulletins</a></li>'."\n";
			}
			$menus .= '   </ul>'."\n";
			$menus .= '</li>'."\n";
		}
		elseif (getSettingValue("active_module_absence") == '2') {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs2/index.php"'.insert_confirm_abandon().'>&nbsp;Absences</a></li>'."\n";
		}

		if (getSettingAOui("active_mod_abs_prof")) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs_prof/index.php" '.insert_confirm_abandon().'>Abs.profs</a></li>'."\n";
		}

		//=======================================================
		// Module Cahier de textes
		if (getSettingValue("active_cahiers_texte") == 'y') {
			if((getSettingAOui('GepiAccesCdtCpe'))||(getSettingAOui('GepiAccesCdtCpeRestreint'))) {
				if(getSettingValue('GepiCahierTexteVersion')==2) {
					$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
					$menus .= '   <ul class="niveau2">'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";
				}
				else {
					$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
					$menus .= '   <ul class="niveau2">'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";
				}
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/extract_tag.php"'.insert_confirm_abandon().' title="Extraire les notices portant tel ou tel tag (contrôle, EPI, AP,...)">Extraction tag</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}
		//=======================================================

		if(getSettingValue("active_carnets_notes") == 'y'){
			//=======================================================
			// Bulletins
			if (getSettingValue("active_bulletins") == "y") {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/bulletin/bulletins_et_conseils_classes.php"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";

				if(getSettingAOui('GepiCpeImprBul')) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/bull_index.php"'.insert_confirm_abandon().'>Impression</a></li>'."\n";
					//$menus .= '     <li><a href="'.$gepiPath.'/bulletin/impression_avis_grp.php"'.insert_confirm_abandon().'>Avis groupes/classes</a></li>'."\n";
				}

				$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Moyennes une classe</a></li>'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a></li>'."\n";
				if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
					$menus .= '     <li><a href="'.$gepiPath.'/absences/index.php"'.insert_confirm_abandon().'>Absences bulletins</a></li>'."\n";
				}
				$menus .= '     <li class="plus"><a href="'.$gepiPath.'/visualisation/index.php"'.insert_confirm_abandon().'>Outils graphiques</a>'."\n";
				$menus .= '            <ul class="niveau3">'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?type_graphe=courbe"'.insert_confirm_abandon().'>Courbe</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?type_graphe=etoile"'.insert_confirm_abandon().'>Etoile</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/eleve_classe.php"'.insert_confirm_abandon().'>Elève/classe</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/eleve_eleve.php"'.insert_confirm_abandon().'>Elève/élève</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/evol_eleve.php"'.insert_confirm_abandon().'>Evol. élève année</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/evol_eleve_classe.php"'.insert_confirm_abandon().'>Evol. élève/classe année</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/stats_classe.php"'.insert_confirm_abandon().'>Evol. moyennes classes</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/visualisation/classe_classe.php"'.insert_confirm_abandon().'>Classe/classe</a></li>'."\n";
				$menus .= '            </ul>'."\n";
				$menus .= '     </li>'."\n";

				if(getSettingAOui('active_mod_engagements')) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_engagements/extraction_engagements.php" '.insert_confirm_abandon().'>Extraction engagements</a></li>'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/mod_engagements/imprimer_documents.php" '.insert_confirm_abandon().'>Convocation conseil de classe,...</a></li>'."\n";
				}

				if((getSettingAOui('active_mod_orientation'))&&((getSettingAOui('OrientationSaisieTypeCpe'))||(getSettingAOui('OrientationSaisieOrientationCpe'))||(getSettingAOui('OrientationSaisieVoeuxCpe')))) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_orientation/index.php" '.insert_confirm_abandon().'>Orientation</a></li>'."\n";
				}

				if (getSettingValue("active_module_absence") == 'y') {
					if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
						$menus .= '     <li><a href="'.$gepiPath.'/absences/index.php"'.insert_confirm_abandon().'>Absences bulletins</a></li>'."\n";
					}
				}
				elseif (getSettingValue("active_module_absence") == '2') {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_abs2/saisie_bulletin.php"'.insert_confirm_abandon().'>Saisie Vie Scolaire</a></li>'."\n";
				}

				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			//=======================================================
	
			//=======================================================
			// Carnets de notes
			$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Carnets de notes</a>'."\n";
			$menus .= '   <ul class="niveau2">'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_bis.php"'.insert_confirm_abandon().'>Relevés de notes</a></li>'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/cahier_notes/index2.php"'.insert_confirm_abandon().'>Moyennes des CN</a></li>'."\n";
			$menus .= '   </ul>'."\n";
			$menus .= '</li>'."\n";
			//=======================================================
		}

		//=======================================================
		// Composantes du Socle

		if(getSettingAOui("SocleSaisieComposantes")) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/saisie/socle_verif.php" '.insert_confirm_abandon().' title="Vérifier le remplissage des bilans de composantes du Socle">Socle</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";

				if(getSettingAOui("SocleSaisieComposantes_".$_SESSION["statut"])) {
					$menus .= '      <a href="'.$gepiPath.'/saisie/saisie_socle.php" '.insert_confirm_abandon().' title="Saisir les bilans de composantes du Socle">Saisie&nbsp;Socle</a>'."\n";
				}
				if(getSettingAOui("SocleOuvertureSaisieComposantes_".$_SESSION["statut"])) {
					$menus .= '      <a href="'.$gepiPath.'/saisie/socle_verrouillage.php" '.insert_confirm_abandon().' title="Ouvrir/verrouiller la saisie des bilans de composantes du Socle">Verrouillage&nbsp;Socle</a>'."\n";
				}

				$menus .= '      <a href="'.$gepiPath.'/saisie/socle_verif.php" '.insert_confirm_abandon().' title="Vérifier le remplissage des bilans de composantes du Socle">Vérification&nbsp;remplissage</a>'."\n";

				if((getSettingAOui("SocleImportComposantes"))&&(getSettingAOui("SocleImportComposantes_".$_SESSION['statut']))) {
					$menus .= '      <a href="'.$gepiPath.'/saisie/socle_import.php" '.insert_confirm_abandon().' title="Importer les bilans de composantes du Socle d\'après SACoche">Import&nbsp;Socle</a>'."\n";
				}

				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
		}

		//=======================================================
		// Module emploi du temps
		if (getSettingValue("autorise_edt_tous") == "y") {
			if(getSettingValue('edt_version_defaut')=="2") {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index2.php?mode=reinit"'.insert_confirm_abandon().'>Emploi du temps</a>'."\n";

				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt/index2.php"'.insert_confirm_abandon().'>EDT prof/classe/élève</a></li>'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=salle1"'.insert_confirm_abandon().'>EDT salle</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			else {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=classe1"'.insert_confirm_abandon().'>Emploi du tps</a>'."\n";

				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=classe1"'.insert_confirm_abandon().'>EDT classe</a></li>'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1"'.insert_confirm_abandon().'>EDT prof</a></li>'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=salle1"'.insert_confirm_abandon().'>EDT salle</a></li>'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=eleve1"'.insert_confirm_abandon().'>EDT élève</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}

		if(getSettingAOui('active_edt_ical')) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
		}
		//=======================================================

		//=======================================================
		// Module discipline
		if (getSettingValue("active_mod_discipline")=='y') {
			$temoin_disc="";
			if((getPref($_SESSION['login'], 'DiscTemoinIncidentCpe', "n")=="y")||(getPref($_SESSION['login'], 'DiscTemoinIncidentCpeTous', "n")=="y")) {
				$cpt_disc=get_temoin_discipline_personnel();
				if($cpt_disc>0) {
					$DiscTemoinIncidentTaille=getPref($_SESSION['login'], 'DiscTemoinIncidentTaille', 16);
					$temoin_disc=" <img src='$gepiPath/images/icons/flag2.gif' width='$DiscTemoinIncidentTaille' height='$DiscTemoinIncidentTaille' title=\"Un ou des ".$mod_disc_terme_incident."s ($cpt_disc) ont été saisis dans les dernières 24h ou depuis votre dernière connexion.\" />";
				}
			}
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/index.php"'.insert_confirm_abandon().'>Discipline</a>'.$temoin_disc.'</li>'."\n";
		}
		//=======================================================

		//=======================================================
		// Elèves
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Élèves</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consultation élève</a></li>'."\n";
		if (getSettingAOui('GepiAccesTouteFicheEleveCpe')) {
			$menus .= '       <li><a href="'.$gepiPath.'/eleves/index.php"'.insert_confirm_abandon().'>Gestion fiches élèves</a></li>'."\n";
		}

		if(acces_modif_liste_eleves_grp_groupes()) {
			$groupe_de_groupes=getSettingValue('denom_groupe_de_groupes');
			if($groupe_de_groupes=="") {
				$groupe_de_groupes="groupe de groupes";
			}

			$groupes_de_groupes=getSettingValue('denom_groupes_de_groupes');
			if($groupes_de_groupes=="") {
				$groupes_de_groupes="groupes de groupes";
			}

			$menus .= '       <li class="plus"><a href="'.$gepiPath.'/groupes/grp_groupes_edit_eleves.php"'.insert_confirm_abandon().' title="Administrer les '.$groupes_de_groupes.' pour modifier les inscriptions élèves.">'.ucfirst($groupes_de_groupes).'</a>'."\n";

			$menus .= '       <ul class="niveau3">'."\n";
			$menus .= '           <li><a href="'.$gepiPath.'/groupes/grp_groupes_edit_eleves.php"'.insert_confirm_abandon().' title="Administrer les '.$groupes_de_groupes.' pour modifier les inscriptions élèves.">'.ucfirst($groupes_de_groupes).'</a></li>'."\n";
			$menus .= '           <li><a href="'.$gepiPath.'/groupes/repartition_ele_grp.php"'.insert_confirm_abandon().' title="Répartir les élèves des groupes d un '.$groupe_de_groupes.' entre les différents groupes/enseignements.">Répartir entre plusieurs groupes</a></li>'."\n";
			$menus .= '       </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		if(getSettingValue('active_module_trombinoscopes')=='y') {
			$menus .= '       <li class="plus"><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscopes</a>'."\n";
			$menus .= '            <ul class="niveau3">'."\n";
			for($loop=0;$loop<count($tmp_liste_classes_cpe);$loop++) {
				$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombino_pdf.php?classe='.$tmp_liste_classes_cpe[$loop]['id'].'&amp;groupe=&amp;equipepeda=&amp;discipline=&amp;statusgepi=&amp;affdiscipline="'.insert_confirm_abandon().' target="_blank">'.$tmp_liste_classes_cpe[$loop]['classe'].' ('.$tmp_liste_classes_cpe[$loop]['nom_complet'].')</a></li>'."\n";
			}
			$menus .= '            </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		if(getSettingAOui('active_mod_engagements')) {
			$menus .= '       <li class="plus"><a href="#">Engagements</a>'."\n";
			$menus .= '         <ul class="niveau3">'."\n";
			if($acces_saisie_engagement=="y") {
				$menus .= '           <li><a href="'.$gepiPath.'/mod_engagements/saisie_engagements.php" '.insert_confirm_abandon().' title="Saisir les engagements élèves/responsables.">Saisie engagements</a></li>'."\n";
			}
			$menus .= '           <li><a href="'.$gepiPath.'/mod_engagements/imprimer_documents.php" '.insert_confirm_abandon().' title="Imprimer les documents, convocations,...">Convocation conseil de classe,...</a></li>'."\n";
			$menus .= '           <li><a href="'.$gepiPath.'/mod_engagements/extraction_engagements.php" '.insert_confirm_abandon().' title="Extraire en CSV, envoyer par mail.">Extraction engagements</a></li>'."\n";
			$menus .= '         </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		if ((getSettingAOui('active_mod_genese_classes'))&&(getSettingAOui('geneseClassesSaisieProfilsCpe'))) {
			$menus .= '       <li><a href="'.$gepiPath.'/mod_genese_classes/saisie_profils_eleves.php"'.insert_confirm_abandon().'>Saisie profils élèves pour classes futures</a></li>'."\n";
		}

		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/eleves/recherche.php"'.insert_confirm_abandon().' title="Effectuer une recherche sur une personne (élève, responsable ou personnel)">&nbsp;Rechercher</a>'."</li>\n";

		//=======================================================
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Listes</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php"'.insert_confirm_abandon().'>Visu. équipes péda</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_groupes_prof.php" '.insert_confirm_abandon().' title="Consulter les enseignements d\'un prof.">Enseign.tel prof</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Visu. mes élèves</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/mod_ooo/publipostage_ooo.php"'.insert_confirm_abandon().'>Publipostage OOo</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Impression PDF listes</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Export CSV listes</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/statistiques/index.php"'.insert_confirm_abandon().'>Statistiques</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		$menus .= $barre_plugin;

		$tbs_menu_cpe[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_cpe[]=array("li"=> $menus);

	}
	//print_r($tbs_menu_cpe);
?>
