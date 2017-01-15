<?php

/**
 * Fichier qui permet de construire la barre de menu autre des pages utilisant un gabarit
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

	
	if ($_SESSION['statut'] == "autre") {

		$tmp_liste_classes_autre=array();
		$sql="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe;"; 

		$res_tmp_liste_classes_autre = mysqli_query($mysqli, $sql);
		if($res_tmp_liste_classes_autre->num_rows > 0) {
			$tmp_cpt_classes_autre=0;
			while($lig_tmp_liste_classes_autre = $res_tmp_liste_classes_autre->fetch_object() ){
				$tmp_liste_classes_autre[$tmp_cpt_classes_autre]=array();
				$tmp_liste_classes_autre[$tmp_cpt_classes_autre]['id']=$lig_tmp_liste_classes_autre->id;
				$tmp_liste_classes_autre[$tmp_cpt_classes_autre]['classe']=$lig_tmp_liste_classes_autre->classe;
				$tmp_liste_classes_autre[$tmp_cpt_classes_autre]['nom_complet']=$lig_tmp_liste_classes_autre->nom_complet;
				$tmp_cpt_classes_autre++;
			}
		}

		$menus = null;

		if((getSettingValue("active_module_absence") == 'y')) {
			$acces_mod_absences_gestion_gestion_absences=acces("/mod_absences/gestion/gestion_absences.php", $_SESSION['statut']);
			$acces_mod_absences_gestion_voir_absences_viescolaire=acces("/mod_absences/gestion/voir_absences_viescolaire.php", $_SESSION['statut']);
			$acces_mod_absences_professeurs_prof_ajout_abs=acces("/mod_absences/professeurs/prof_ajout_abs.php", $_SESSION['statut']);

			if($acces_mod_absences_gestion_gestion_absences||$acces_mod_absences_gestion_voir_absences_viescolaire||$acces_mod_absences_professeurs_prof_ajout_abs) {
				$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Absences</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				if($acces_mod_absences_gestion_gestion_absences) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_absences/gestion/gestion_absences.php"'.insert_confirm_abandon().'>Gestion absences</a></li>'."\n";
				}
				if($acces_mod_absences_gestion_voir_absences_viescolaire) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_absences/gestion/voir_absences_viescolaire.php"'.insert_confirm_abandon().'>Visu. absences</a></li>'."\n";
				}
				if($acces_mod_absences_professeurs_prof_ajout_abs) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_absences/professeurs/prof_ajout_abs.php"'.insert_confirm_abandon().'>Saisir/gérer absences</a></li>'."\n";
				}
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}
		elseif (getSettingValue("active_module_absence") == '2') {
			if(acces("/mod_abs2/index.php", $_SESSION['statut'])) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs2/index.php"'.insert_confirm_abandon().'>&nbsp;Absences</a></li>'."\n";
			}
		}

		/*
		if (getSettingAOui("active_mod_abs_prof")) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs_prof/index.php" '.insert_confirm_abandon().'>Abs.profs</a></li>'."\n";
		}
		*/

		//=======================================================
		// Module Cahier de textes
		if (getSettingValue("active_cahiers_texte") == 'y') {
			$acces_cahier_texte_2_extract_tag=acces("/cahier_texte_2/extract_tag.php", $_SESSION['statut']);
			$acces_documents_archives_index=acces("/documents/archives/index.php", $_SESSION['statut']);

			if(getSettingValue('GepiCahierTexteVersion')==2) {
				$acces_cahier_texte_2_see_all=acces("/cahier_texte_2/see_all.php", $_SESSION['statut']);

				if($acces_cahier_texte_2_see_all) {
					$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
					$menus .= '   <ul class="niveau2">'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/see_all.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";

					if($acces_cahier_texte_2_extract_tag) {
						$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/extract_tag.php"'.insert_confirm_abandon().' title="Extraire les notices portant tel ou tel tag (contrôle, EPI, AP,...)">Extraction tag</a></li>'."\n";
					}
					if($acces_documents_archives_index) {
						$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
					}
					$menus .= '   </ul>'."\n";
					$menus .= '</li>'."\n";
				}
				elseif($acces_cahier_texte_2_extract_tag||$acces_documents_archives_index) {
					$menus .= '<li class="li_inline">&nbsp;CDT'."\n";
					$menus .= '   <ul class="niveau2">'."\n";

					if($acces_cahier_texte_2_extract_tag) {
						$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/extract_tag.php"'.insert_confirm_abandon().' title="Extraire les notices portant tel ou tel tag (contrôle, EPI, AP,...)">Extraction tag</a></li>'."\n";
					}
					if($acces_documents_archives_index) {
						$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
					}
					$menus .= '   </ul>'."\n";
					$menus .= '</li>'."\n";
				}
			}
			else {
				$acces_cahier_texte_see_all=acces("/cahier_texte/see_all.php", $_SESSION['statut']);
				if($acces_cahier_texte_see_all) {
					$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
					$menus .= '   <ul class="niveau2">'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";

					if($acces_cahier_texte_2_extract_tag) {
						$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/extract_tag.php"'.insert_confirm_abandon().' title="Extraire les notices portant tel ou tel tag (contrôle, EPI, AP,...)">Extraction tag</a></li>'."\n";
					}
					if($acces_documents_archives_index) {
						$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
					}
					$menus .= '   </ul>'."\n";
					$menus .= '</li>'."\n";
				}
				elseif($acces_cahier_texte_2_extract_tag||$acces_documents_archives_index) {
					$menus .= '<li class="li_inline">&nbsp;CDT'."\n";
					$menus .= '   <ul class="niveau2">'."\n";
					if($acces_cahier_texte_2_extract_tag) {
						$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_2/extract_tag.php"'.insert_confirm_abandon().' title="Extraire les notices portant tel ou tel tag (contrôle, EPI, AP,...)">Extraction tag</a></li>'."\n";
					}
					if($acces_documents_archives_index) {
						$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
					}
					$menus .= '   </ul>'."\n";
					$menus .= '</li>'."\n";
				}
			}

		}
		//=======================================================

		if(getSettingValue("active_carnets_notes") == 'y') {
			$acces_cahier_notes_visu_releve_notes_2=acces("/cahier_notes/visu_releve_notes_2.php", $_SESSION['statut']);
			$acces_cahier_notes_visu_releve_notes=acces("/cahier_notes/visu_releve_notes.php", $_SESSION['statut']);
			$acces_cahier_notes_visu_releve_notes_bis=acces("/cahier_notes/visu_releve_notes_bis.php", $_SESSION['statut']);
			$acces_cahier_notes_index2=acces("/cahier_notes/index2.php", $_SESSION['statut']);

			if($acces_cahier_notes_visu_releve_notes_2||$acces_cahier_notes_visu_releve_notes||$acces_cahier_notes_visu_releve_notes_bis||$acces_cahier_notes_index2) {
				$menus .= '<li class="li_inline">&nbsp;Carnet de notes'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				if($acces_cahier_notes_visu_releve_notes_2) {
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_2.php"'.insert_confirm_abandon().'>Relevé de notes</a></li>'."\n";
				}
				if($acces_cahier_notes_visu_releve_notes) {
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes.php"'.insert_confirm_abandon().'>Relevé de notes</a></li>'."\n";
				}
				if($acces_cahier_notes_visu_releve_notes_bis) {
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/visu_releve_notes_bis.php"'.insert_confirm_abandon().'>Relevé de notes</a></li>'."\n";
				}
				if($acces_cahier_notes_index2) {
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_notes/index2.php"'.insert_confirm_abandon().'>Visu.moy.CN</a></li>'."\n";
				}
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}

		if(getSettingValue("active_bulletins") == 'y') {
			$acces_bulletin_verif_bulletins=acces("/bulletin/verif_bulletins.php", $_SESSION['statut']);
			$acces_bulletin_verrouillage=acces("/bulletin/verrouillage.php", $_SESSION['statut']);
			$acces_classes_acces_appreciations=acces("/classes/acces_appreciations.php", $_SESSION['statut']);
			$acces_bulletin_param_bull_pdf=acces("/bulletin/param_bull_pdf.php", $_SESSION['statut']);
			$acces_bulletin_param_bull=acces("/bulletin/param_bull.php", $_SESSION['statut']);
			$acces_bulletin_bull_index=acces("/bulletin/bull_index.php", $_SESSION['statut']);
			$acces_prepa_conseil_index3=acces("/prepa_conseil/index3.php", $_SESSION['statut']);
			$acces_prepa_conseil_index2=acces("/prepa_conseil/index2.php", $_SESSION['statut']);
			$acces_absences_index=acces("/absences/index.php", $_SESSION['statut']);
			$acces_mod_abs2_saisie_bulletin=acces("/mod_abs2/saisie_bulletin.php", $_SESSION['statut']);
			$acces_visualisation_index=acces("/visualisation/index.php", $_SESSION['statut']);
			$acces_visualisation_affiche_eleve=acces("/visualisation/affiche_eleve.php", $_SESSION['statut']);
			$acces_visualisation_eleve_classe=acces("/visualisation/eleve_classe.php", $_SESSION['statut']);
			$acces_visualisation_eleve_eleve=acces("/visualisation/eleve_eleve.php", $_SESSION['statut']);
			$acces_visualisation_evol_eleve=acces("/visualisation/evol_eleve.php", $_SESSION['statut']);
			$acces_visualisation_evol_eleve_classe=acces("/visualisation/evol_eleve_classe.php", $_SESSION['statut']);
			$acces_visualisation_stats_classe=acces("/visualisation/stats_classe.php", $_SESSION['statut']);
			$acces_visualisation_classe_classe=acces("/visualisation/classe_classe.php", $_SESSION['statut']);
			//$acces_=acces("/.php", $_SESSION['statut']);

			if($acces_bulletin_verif_bulletins||$acces_bulletin_verrouillage||$acces_classes_acces_appreciations||$acces_bulletin_param_bull_pdf||$acces_bulletin_param_bull||$acces_bulletin_bull_index||$acces_prepa_conseil_index3||$acces_prepa_conseil_index2||$acces_absences_index||$acces_mod_abs2_saisie_bulletin||$acces_visualisation_index||$acces_visualisation_affiche_eleve||$acces_visualisation_eleve_classe||$acces_visualisation_eleve_eleve||$acces_visualisation_evol_eleve||$acces_visualisation_evol_eleve_classe||$acces_visualisation_stats_classe||$acces_visualisation_classe_classe) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/bulletin/bulletins_et_conseils_classes.php"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";

				if($acces_bulletin_verif_bulletins) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/verif_bulletins.php"'.insert_confirm_abandon().'>Outil de vérification</a></li>'."\n";
				}

				if($acces_bulletin_verrouillage) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/verrouillage.php"'.insert_confirm_abandon().'>Verrouillage</a></li>'."\n";
				}

				if($acces_classes_acces_appreciations) {
					$menus .= '     <li><a href="'.$gepiPath.'/classes/acces_appreciations.php"'.insert_confirm_abandon().'>Accès appréciations</a></li>'."\n";
				}

				if($acces_bulletin_param_bull_pdf) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/param_bull_pdf.php"'.insert_confirm_abandon().'>Param.bull.PDF</a></li>'."\n";
				}

				if($acces_bulletin_param_bull) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/param_bull.php"'.insert_confirm_abandon().'>Param.bull</a></li>'."\n";
				}

				if($acces_bulletin_bull_index) {
					$menus .= '     <li><a href="'.$gepiPath.'/bulletin/bull_index.php"'.insert_confirm_abandon().'>Impression</a></li>'."\n";
				}

				if($acces_prepa_conseil_index3) {
					$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a></li>'."\n";
				}

				if($acces_prepa_conseil_index2) {
					$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Moyennes d une classe</a></li>'."\n";
				}

				if($acces_absences_index) {
					if (getSettingValue("active_module_absence") == 'y') {
						if (getSettingValue("active_module_absence")!='2' || getSettingValue("abs2_import_manuel_bulletin")=='y') {
							$menus .= '     <li><a href="'.$gepiPath.'/absences/index.php"'.insert_confirm_abandon().'>Absences bulletins</a></li>'."\n";
						}
					}
				}

				if($acces_mod_abs2_saisie_bulletin) {
					if (getSettingValue("active_module_absence") == '2') {
						$menus .= '     <li><a href="'.$gepiPath.'/mod_abs2/saisie_bulletin.php"'.insert_confirm_abandon().'>Saisie Vie Scolaire</a></li>'."\n";
					}
				}

				if($acces_visualisation_index) {
					$menus .= '     <li class="plus"><a href="'.$gepiPath.'/visualisation/index.php"'.insert_confirm_abandon().'>Outils graphiques</a>'."\n";
					$menus .= '            <ul class="niveau3">'."\n";

					if($acces_visualisation_affiche_eleve) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?type_graphe=courbe"'.insert_confirm_abandon().'>Courbe</a></li>'."\n";
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/affiche_eleve.php?type_graphe=etoile"'.insert_confirm_abandon().'>Etoile</a></li>'."\n";
					}

					if($acces_visualisation_eleve_classe) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/eleve_classe.php"'.insert_confirm_abandon().'>Elève/classe</a></li>'."\n";
					}

					if($acces_visualisation_eleve_eleve) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/eleve_eleve.php"'.insert_confirm_abandon().'>Elève/élève</a></li>'."\n";
					}

					if($acces_visualisation_evol_eleve) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/evol_eleve.php"'.insert_confirm_abandon().'>Evol. élève année</a></li>'."\n";
					}

					if($acces_visualisation_evol_eleve_classe) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/evol_eleve_classe.php"'.insert_confirm_abandon().'>Evol. élève/classe année</a></li>'."\n";
					}

					if($acces_visualisation_stats_classe) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/stats_classe.php"'.insert_confirm_abandon().'>Evol. moyennes classes</a></li>'."\n";
					}

					if($acces_visualisation_classe_classe) {
						$menus .= '                <li><a href="'.$gepiPath.'/visualisation/classe_classe.php"'.insert_confirm_abandon().'>Classe/classe</a></li>'."\n";
					}

					$menus .= '            </ul>'."\n";
					$menus .= '     </li>'."\n";
				}

				/*
				if($acces_) {
					$menus .= '     <li><a href="'.$gepiPath.'/.php"'.insert_confirm_abandon().'></a></li>'."\n";
				}

				if(getSettingAOui('active_mod_engagements')) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_engagements/extraction_engagements.php" '.insert_confirm_abandon().'>Extraction engagements</a></li>'."\n";
					$menus .= '     <li><a href="'.$gepiPath.'/mod_engagements/imprimer_documents.php" '.insert_confirm_abandon().'>Convocation conseil de classe,...</a></li>'."\n";
				}

				if((getSettingAOui('active_mod_orientation'))&&((getSettingAOui('OrientationSaisieTypeCpe'))||(getSettingAOui('OrientationSaisieOrientationCpe'))||(getSettingAOui('OrientationSaisieVoeuxCpe')))) {
					$menus .= '     <li><a href="'.$gepiPath.'/mod_orientation/index.php" '.insert_confirm_abandon().'>Orientation</a></li>'."\n";
				}
				*/

				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}

		//=======================================================
		// Module emploi du temps
		if (getSettingValue("autorise_edt_tous") == "y") {
			$acces_edt_organisation_index_edt=acces("/edt_organisation/index_edt.php", $_SESSION['statut']);
			$acces_edt_index2=acces("/edt/index2.php", $_SESSION['statut']);
			if((getSettingValue('edt_version_defaut')=="2")&&($acces_edt_index2)) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index2.php?mode=reinit"'.insert_confirm_abandon().'>Emploi du temps</a>'."\n";

				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '       <li><a href="'.$gepiPath.'/edt/index2.php"'.insert_confirm_abandon().'>EDT prof/classe/élève</a></li>'."\n";
				if($acces_edt_organisation_index_edt) {
					$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=salle1"'.insert_confirm_abandon().'>EDT salle</a></li>'."\n";
				}
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			elseif($acces_edt_organisation_index_edt) {
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
			$acces_edt_index=acces("/edt/index.php", $_SESSION['statut']);
			if($acces_edt_index) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt/index.php" '.insert_confirm_abandon().' title="Emplois du temps importés à l\'aide de fichiers ICAL/ICS.">EDT Ical/Ics</a></li>'."\n";
			}
		}
		//=======================================================


		//=======================================================
		// Module discipline
		if (getSettingValue("active_mod_discipline")=='y') {
			$acces_mod_discipline_index=acces("/mod_discipline/index.php", $_SESSION['statut']);
			$temoin_disc="";
			/*
			if((getPref($_SESSION['login'], 'DiscTemoinIncidentCpe', "n")=="y")||(getPref($_SESSION['login'], 'DiscTemoinIncidentCpeTous', "n")=="y")) {
				$cpt_disc=get_temoin_discipline_personnel();
				if($cpt_disc>0) {
					$DiscTemoinIncidentTaille=getPref($_SESSION['login'], 'DiscTemoinIncidentTaille', 16);
					$temoin_disc=" <img src='$gepiPath/images/icons/flag2.gif' width='$DiscTemoinIncidentTaille' height='$DiscTemoinIncidentTaille' title=\"Un ou des ".$mod_disc_terme_incident."s ($cpt_disc) ont été saisis dans les dernières 24h ou depuis votre dernière connexion.\" />";
				}
			}
			*/
			if($acces_mod_discipline_index) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/index.php"'.insert_confirm_abandon().'>Discipline</a>'.$temoin_disc.'</li>'."\n";
			}
		}
		//=======================================================

		//=======================================================
		// Elèves
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Élèves</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		if(acces("/eleves/visu_eleve.php", $_SESSION['statut'])) {
			$menus .= '       <li><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consultation élève</a></li>'."\n";
		}
		if(acces("/eleves/index.php", $_SESSION['statut'])) {
			$menus .= '       <li><a href="'.$gepiPath.'/eleves/index.php"'.insert_confirm_abandon().'>Gestion fiches élèves</a></li>'."\n";
		}
		if(acces("/responsables/index.php", $_SESSION['statut'])) {
			$menus .= '       <li><a href="'.$gepiPath.'/eleves/index.php"'.insert_confirm_abandon().'>Gestion fiches responsables</a></li>'."\n";
		}

		/*
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
		*/


		if((getSettingValue('active_module_trombinoscopes')=='y')&&(acces("/mod_trombinoscopes/trombinoscopes.php", $_SESSION['statut']))) {
			$menus .= '       <li class="plus"><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscopes</a>'."\n";
			$menus .= '            <ul class="niveau3">'."\n";
			if(acces("/mod_trombinoscopes/trombino_pdf.php", $_SESSION['statut'])) {
				for($loop=0;$loop<count($tmp_liste_classes_autre);$loop++) {
					$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombino_pdf.php?classe='.$tmp_liste_classes_autre[$loop]['id'].'&amp;groupe=&amp;equipepeda=&amp;discipline=&amp;statusgepi=&amp;affdiscipline="'.insert_confirm_abandon().' target="_blank">'.$tmp_liste_classes_autre[$loop]['classe'].' ('.$tmp_liste_classes_autre[$loop]['nom_complet'].')</a></li>'."\n";
				}
			}
			$menus .= '            </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		/*
		if(getSettingAOui('active_mod_engagements')) {
			$menus .= '       <li class="plus"><a href="#">Engagements</a>'."\n";
			$menus .= '         <ul class="niveau3">'."\n";
			$menus .= '           <li><a href="'.$gepiPath.'/mod_engagements/saisie_engagements.php" '.insert_confirm_abandon().'>Saisie engagements</a></li>'."\n";

			$menus .= '           <li><a href="'.$gepiPath.'/mod_engagements/imprimer_documents.php" '.insert_confirm_abandon().'>Convocation conseil de classe,...</a></li>'."\n";
			$menus .= '         </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		if ((getSettingAOui('active_mod_genese_classes'))&&(getSettingAOui('geneseClassesSaisieProfilsCpe'))) {
			$menus .= '       <li><a href="'.$gepiPath.'/mod_genese_classes/saisie_profils_eleves.php"'.insert_confirm_abandon().'>Saisie profils élèves pour classes futures</a></li>'."\n";
		}
		*/

		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		if(acces("/eleves/recherche.php", $_SESSION['statut'])) {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/eleves/recherche.php"'.insert_confirm_abandon().' title="Effectuer une recherche sur une personne (élève, responsable ou personnel)">&nbsp;Rechercher</a>'."</li>\n";
		}

		//=======================================================

		$acces_groupes_visu_profs_class=acces("/groupes/visu_profs_class.php", $_SESSION['statut']);
		$acces_groupes_visu_groupes_prof=acces("/groupes/visu_groupes_prof.php", $_SESSION['statut']);
		$acces_groupes_visu_mes_listes=acces("/groupes/visu_mes_listes.php", $_SESSION['statut']);
		$acces_mod_ooo_publipostage_ooo=acces("/mod_ooo/publipostage_ooo.php", $_SESSION['statut']);
		$acces_impression_impression_serie=acces("/impression/impression_serie.php", $_SESSION['statut']);
		$acces_groupes_mes_listes=acces("/groupes/mes_listes.php", $_SESSION['statut']);
		$acces_statistiques_index=acces("/statistiques/index.php", $_SESSION['statut']);

		if($acces_groupes_visu_profs_class||$acces_groupes_visu_groupes_prof||$acces_groupes_visu_mes_listes||$acces_mod_ooo_publipostage_ooo||$acces_impression_impression_serie||$acces_groupes_mes_listes||$acces_statistiques_index) {
			$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Listes</a>'."\n";
			$menus .= '   <ul class="niveau2">'."\n";
			if($acces_groupes_visu_profs_class) {
				$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php"'.insert_confirm_abandon().'>Visu. équipes péda</a></li>'."\n";
			}
			if($acces_groupes_visu_groupes_prof) {
				$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_groupes_prof.php" '.insert_confirm_abandon().' title="Consulter les enseignements d\'un prof.">Enseign.tel prof</a></li>'."\n";
			}
			if($acces_groupes_visu_mes_listes) {
				$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Visu. mes élèves</a></li>'."\n";
			}
			if($acces_mod_ooo_publipostage_ooo) {
				$menus .= '       <li><a href="'.$gepiPath.'/mod_ooo/publipostage_ooo.php"'.insert_confirm_abandon().'>Publipostage OOo</a></li>'."\n";
			}
			if($acces_impression_impression_serie) {
				$menus .= '       <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Impression PDF listes</a></li>'."\n";
			}
			if($acces_groupes_mes_listes) {
				$menus .= '       <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Export CSV listes</a></li>'."\n";
			}
			if($acces_statistiques_index) {
				$menus .= '       <li><a href="'.$gepiPath.'/statistiques/index.php"'.insert_confirm_abandon().'>Statistiques</a></li>'."\n";
			}
			$menus .= '   </ul>'."\n";
			$menus .= '</li>'."\n";
		}
		//=======================================================

		$menus .= $barre_plugin;

		$tbs_menu_autre[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_autre[]=array("li"=> $menus);

	}
	//print_r($tbs_menu_cpe);
?>
