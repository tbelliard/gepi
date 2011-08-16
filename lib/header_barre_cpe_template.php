<?php

/*
 * $Id$
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

	//=======================================================
	/*
	// Trame modèle
	$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;</a>'."\n";
	$menus .= '   <ul class="niveau2">'."\n";
	$menus .= '       <li><a href="'.$gepiPath.'"'.insert_confirm_abandon().'></a></li>'."\n";
	$menus .= '       <li class="plus"><a href="'.$gepiPath.'"'.insert_confirm_abandon().'></a>'."\n";
	$menus .= '           <ul class="niveau3">'."\n";
	$menus .= '                <li><a href="'.$gepiPath.'"'.insert_confirm_abandon().'></a></li>'."\n";
	$menus .= '           </ul>'."\n";
	$menus .= '       </li>'."\n";
	$menus .= '   </ul>'."\n";
	$menus .= '</li>'."\n";
	*/
	//=======================================================

	
	if ($_SESSION['statut'] == "cpe") {

		$menus = null;

		//=======================================================
		// Module Cahier de textes
		if (getSettingValue("active_cahiers_texte") == 'y') {
			if(getSettingValue('GepiCahierTexteVersion')==2) {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte_2/see_all2.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte/see_all2.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";
			}
			else {
				$menus .= '<li class="li_inline"><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>&nbsp;CDT</a>'."\n";
				$menus .= '   <ul class="niveau2">'."\n";
				$menus .= '     <li><a href="'.$gepiPath.'/cahier_texte/see_all.php"'.insert_confirm_abandon().'>Consultation CDT</a></li>'."\n";
			}
			$menus .= '   </ul>'."\n";
			$menus .= '</li>'."\n";
		}
		//=======================================================

		if(getSettingValue("active_carnets_notes") == 'y'){
			//=======================================================
			// Bulletins
			$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Bulletins</a>'."\n";
			$menus .= '   <ul class="niveau2">'."\n";
	
			$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index2.php"'.insert_confirm_abandon().'>Moyennes une classe</a></li>'."\n";
			$menus .= '     <li><a href="'.$gepiPath.'/prepa_conseil/index3.php"'.insert_confirm_abandon().'>Bulletins simplifiés</a></li>'."\n";
	
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
		// Gestion
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Elèves</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/eleves/visu_eleve.php"'.insert_confirm_abandon().'>Consultation elève</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/mod_trombinoscopes/trombinoscopes.php"'.insert_confirm_abandon().'>Trombinoscopes</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		//=======================================================
		$menus .= '<li class="li_inline"><a href="#"'.insert_confirm_abandon().'>&nbsp;Listes</a>'."\n";
		$menus .= '   <ul class="niveau2">'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_profs_class.php"'.insert_confirm_abandon().'>Visu. équipes péda</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/visu_mes_listes.php"'.insert_confirm_abandon().'>Visu. mes élèves</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/mod_ooo/publipostage_ooo.php"'.insert_confirm_abandon().'>Publipostage OOo</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/impression/impression_serie.php"'.insert_confirm_abandon().'>Impression PDF listes</a></li>'."\n";
		$menus .= '       <li><a href="'.$gepiPath.'/groupes/mes_listes.php"'.insert_confirm_abandon().'>Export CSV listes</a></li>'."\n";
		$menus .= '   </ul>'."\n";
		$menus .= '</li>'."\n";
		//=======================================================

		//$menus='<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n".$menus;

		$menus .= $barre_plugin;

		$tbs_menu_cpe[]=array("li"=> '<li class="li_inline"><a href="'.$gepiPath.'/accueil.php"'.insert_confirm_abandon().'>Accueil</a></li>'."\n");		
		$tbs_menu_cpe[]=array("li"=> $menus);

	}
	//print_r($tbs_menu_cpe);
?>
