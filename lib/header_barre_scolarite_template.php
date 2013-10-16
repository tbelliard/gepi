<?php

/**
 * Fichier qui permet de construire la barre de menu scolarité
 * 
 *
 * Variables envoyées au gabarit : liens de la barre de menu scolarité
 * - $tbs_menu_admin = array(li)
 *
 * @license GNU/GPL v2
 * @package General
 * @subpackage Affichage
 * @see getSettingValue()
 * @see insert_confirm_abandon()
 * @see menu_plugins()
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

	if ($_SESSION['statut'] == "scolarite") {

		$tmp_liste_classes_scol=array();
		$sql="SELECT DISTINCT id, classe, nom_complet FROM classes ORDER BY classe;";
		$res_tmp_liste_classes_scol=mysql_query($sql);
		if(mysql_num_rows($res_tmp_liste_classes_scol)>0) {
			$tmp_cpt_classes_scol=0;
			while($lig_tmp_liste_classes_scol=mysql_fetch_object($res_tmp_liste_classes_scol)) {
				$tmp_liste_classes_scol[$tmp_cpt_classes_scol]=array();
				$tmp_liste_classes_scol[$tmp_cpt_classes_scol]['id']=$lig_tmp_liste_classes_scol->id;
				$tmp_liste_classes_scol[$tmp_cpt_classes_scol]['classe']=$lig_tmp_liste_classes_scol->classe;
				$tmp_liste_classes_scol[$tmp_cpt_classes_scol]['nom_complet']=$lig_tmp_liste_classes_scol->nom_complet;
				$tmp_cpt_classes_scol++;
			}
		}

		$menus = null;

		//=======================================================
		// Module Absences
		if (getSettingValue("active_module_absence") == 'y') {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_absences/gestion/voir_absences_viescolaire.php"'.insert_confirm_abandon().'>Absences</a></li>'."\n";
		}
		elseif (getSettingValue("active_module_absence") == '2') {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_abs2/index.php"'.insert_confirm_abandon().'>&nbsp;Absences</a></li>'."\n";
		}

		//=======================================================
		// Module Cahier de textes
		if (getSettingValue("active_cahiers_texte") == 'y') {
			if((getSettingAOui('GepiAccesCdtScol'))||(getSettingAOui('GepiAccesCdtScolRestreint'))) {
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
				if(getSettingValue('GepiAccesCdtVisa')=='yes') {
					$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_admin/visa_ct.php"'.insert_confirm_abandon().'>Visa c. de textes</a></li>'."\n";
				}
				$menus .= '     <li><a href="'.$gepiPath.'/documents/archives/index.php"'.insert_confirm_abandon().'>Archives CDT</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
			elseif(getSettingValue('GepiAccesCdtVisa')=='yes') {
				$menus .= '<li class="li_inline">&nbsp;CDT'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte_admin/visa_ct.php"'.insert_confirm_abandon().'>Visa c. de textes</a></li>'."\n";
				$menus .= '   </ul>'."\n";
				$menus .= '</li>'."\n";
			}
		}
		//=======================================================

		if(getSettingValue("active_carnets_notes") == 'y'){
			//=======================================================
			// Bulletins
			if (getSettingValue("active_bulletins") == "y") {
				$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
	
				$menus .= '     <li class="plus">Avis conseil classe'."\n";
				$menus .= '            <ul class="niveau3">'."\n";
				if(getSettingValue('GepiRubConseilScol')=='yes') {
					$menus .= '                <li><a href="'.$gepiPath.'/saisie/saisie_avis.php"'.insert_confirm_abandon().'>Saisie des avis Conseil</a></li>'."\n";
				}
				if(getSettingValue('CommentairesTypesScol')=='yes') {
					$menus .= '                <li><a href="'.$gepiPath.'/saisie/commentaires_types.php"'.insert_confirm_abandon().'>Commentaires-types</a></li>'."\n";
				}
				$menus .= '                <li><a href="'.$gepiPath.'/saisie/impression_avis.php"'.insert_confirm_abandon().'>Impression avis PDF</a></li>'."\n";
				$menus .= '            </ul>'."\n";
				$menus .= '     </li>'."\n";
	
				$menus .= '     <li class="plus">Vérif. et accès'."\n";
				$menus .= '            <ul class="niveau3">'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/bulletin/verif_bulletins.php"'.insert_confirm_abandon().'>Vérif. remplissage bull</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/bulletin/verrouillage.php"'.insert_confirm_abandon().'>Verrouillage périodes</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/bulletin/autorisation_exceptionnelle_saisie_app.php"'.insert_confirm_abandon().'>Autorisation exceptionnelle de remplissage</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/classes/acces_appreciations.php"'.insert_confirm_abandon().'>Accès resp/ele appréciations</a></li>'."\n";
				$menus .= '            </ul>'."\n";
				$menus .= '     </li>'."\n";
	
	
				$menus .= '     <li class="plus">Bulletins'."\n";
				$menus .= '            <ul class="niveau3">'."\n";
				if(getSettingValue('GepiScolImprBulSettings')=='yes') {
				if(getSettingValue('type_bulletin_par_defaut')=="pdf") {
					$menus .= '                <li><a href="'.$gepiPath.'/bulletin/param_bull_pdf.php" '.insert_confirm_abandon().'>Param. impression bull</a></li>'."\n";
				}
				else {
					$menus .= '                <li><a href="'.$gepiPath.'/bulletin/param_bull.php" '.insert_confirm_abandon().'>Param. impression bull</a></li>'."\n";
				}
				}
				$menus .= '                <li><a href="'.$gepiPath.'/bulletin/bull_index.php"'.insert_confirm_abandon().'>Impression bulletins</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a></li>'."\n";
				$menus .= '            </ul>'."\n";
				$menus .= '     </li>'."\n";
	
				$menus .= '     <li class="plus">Moyennes'."\n";
				$menus .= '            <ul class="niveau3">'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/prepa_conseil/index1.php"'.insert_confirm_abandon().'>Mes moyennes et app.</a></li>'."\n";
				$menus .= '                <li><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Moyennes une classe</a></li>'."\n";
				$menus .= '            </ul>'."\n";
				$menus .= '     </li>'."\n";
	
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
		// Module emploi du temps
		if (getSettingValue("autorise_edt_tous") == "y") {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=classe1"'.insert_confirm_abandon().'>Emploi du tps</a>'."\n";

			$menus .= '   <ul class="niveau2">'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=classe1"'.insert_confirm_abandon().'>EDT classe</a></li>'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1"'.insert_confirm_abandon().'>EDT prof</a></li>'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=salle1"'.insert_confirm_abandon().'>EDT salle</a></li>'."\n";
			$menus .= '       <li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=eleve1"'.insert_confirm_abandon().'>EDT élève</a></li>'."\n";
			$menus .= '   </ul>'."\n";
			$menus .= '</li>'."\n";
		}
		//=======================================================

		//=======================================================
		// Module discipline
		if (getSettingValue("active_mod_discipline")=='y') {
			$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/mod_discipline/index.php"'.insert_confirm_abandon().'>Discipline</a></li>'."\n";
		}
		//=======================================================

		//=======================================================
		// Gestion
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Gestion</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li class="plus"><a href="'.$gepiPath.'/eleves/index.php"'.insert_confirm_abandon().'>Elèves</a>'."\n";
		$menus .= '           <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/index.php"'.insert_confirm_abandon().'>Gestion élèves</a></li>'."\n";
		//$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import2.php"'.insert_confirm_abandon().'>Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consultation elève</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/classes/acces_appreciations.php"'.insert_confirm_abandon().'>Accès appréciations</a></li>'."\n";

		if(getSettingValue('active_module_trombinoscopes')=='y') {
			$menus .= '       <li class="plus"><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscopes</a>'."\n";
			$menus .= '            <ul class="niveau4">'."\n";
			for($loop=0;$loop<count($tmp_liste_classes_scol);$loop++) {
				$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombino_pdf.php?classe='.$tmp_liste_classes_scol[$loop]['id'].'&amp;groupe=&amp;equipepeda=&amp;discipline=&amp;statusgepi=&amp;affdiscipline="'.insert_confirm_abandon().' target="_blank">'.$tmp_liste_classes_scol[$loop]['classe'].' ('.$tmp_liste_classes_scol[$loop]['nom_complet'].')</a></li>'."\n";
			}
			$menus .= '            </ul>'."\n";
			$menus .= '       </li>'."\n";
		}

		$menus .= '            </ul>'."\n";
		$menus .= '       </li>'."\n";
		$menus .= '       <li class="plus"><a href="'.$gepiPath.'/responsables/index.php"'.insert_confirm_abandon().'>Responsables</a>'."\n";
		$menus .= '           <ul class="niveau3">'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/responsables/index.php"'.insert_confirm_abandon().'>Gestion responsables</a></li>'."\n";
		//$menus .= '                <li><a href="'.$gepiPath.'/responsables/maj_import2.php"'.insert_confirm_abandon().'>Mise à jour Sconet</a></li>'."\n";
		$menus .= '                <li><a href="'.$gepiPath.'/classes/acces_appreciations.php"'.insert_confirm_abandon().'>Accès appréciations</a></li>'."\n";
		$menus .= '            </ul>'."\n";
		$menus .= '       </li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/messagerie/index.php"'.insert_confirm_abandon().'>Panneau affichage</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/statistiques/index.php"'.insert_confirm_abandon().'>Statistiques</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		//=======================================================
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Listes</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php"'.insert_confirm_abandon().'>Visu. équipes péda</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Visu. mes élèves</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/mod_ooo/publipostage_ooo.php"'.insert_confirm_abandon().' title="Publipostage au format OpenOffice.org d\'après des données élèves">Publipostage OOo</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Impression PDF listes</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Export CSV listes</a></li>'."\n";
		if(getSettingValue('active_module_trombinoscopes')=='y') {
			$menus .= '       <li class="plus"><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscopes</a>'."\n";
			$menus .= '            <ul class="niveau3">'."\n";
			for($loop=0;$loop<count($tmp_liste_classes_scol);$loop++) {
				$menus .= '                <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombino_pdf.php?classe='.$tmp_liste_classes_scol[$loop]['id'].'&amp;groupe=&amp;equipepeda=&amp;discipline=&amp;statusgepi=&amp;affdiscipline="'.insert_confirm_abandon().' target="_blank">'.$tmp_liste_classes_scol[$loop]['classe'].' ('.$tmp_liste_classes_scol[$loop]['nom_complet'].')</a></li>'."\n";
			}
			$menus .= '            </ul>'."\n";
			$menus .= '       </li>'."\n";
		}
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		//$menus='<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n".$menus;

		$menus .= $barre_plugin;

		$tbs_menu_scol[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_scol[]=array("li"=> $menus);	

	}

?>
