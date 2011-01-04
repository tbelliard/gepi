<?php

/*
 * $Id: header_barre_prof_template.php $
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
* $tbs_menu_prof										liens se la barre de menu prof
*				-> lien
*				-> texte
*

$TBS->MergeBlock('tbs_menu_prof',$tbs_menu_prof) ;

unset($tbs_menu_prof);
*/
 
// ====== SECURITE =======

if (!$_SESSION["login"]) {
    header("Location: ../logout.php?auto=2");
    die();
}

	// Pour permettre d'utiliser le module EdT avec les autres modules
	$groupe_abs = $groupe_text = '';
	if (getSettingValue("autorise_edt_tous") == "y") {
		// Actuellement, ce professeur à ce cours (id_cours):
		$cours_actu = retourneCours($_SESSION["login"]);
		// Qui correspond à cet id_groupe :
		if ($cours_actu != "non") {
			$queryG = mysql_query("SELECT id_groupe, id_aid FROM edt_cours WHERE id_cours = '".$cours_actu."'");
			$groupe_actu = mysql_fetch_array($queryG);
			// Il faudrait vérifier si ce n'est pas une AID
			if ($groupe_actu["id_aid"] != NULL) {
				$groupe_abs = '?groupe=AID|'.$groupe_actu["id_aid"].'&amp;menuBar=ok';
				$groupe_text = '';
			}else{
				$groupe_text = '?id_groupe='.$groupe_actu["id_groupe"].'&amp;year='.date("Y").'&amp;month='.date("n").'&amp;day='.date("d").'&amp;edit_devoir=';
				$groupe_abs = '?groupe='.$groupe_actu["id_groupe"].'&amp;menuBar=ok';
			}
		}
	}

/* On fixe l'ensemble des modules qui sont ouverts pour faire la liste des <li> */
	// module absence
	if (getSettingValue("active_module_absence_professeur")=='y') {
		//$barre_absence = '<li><a href="'.$gepiPath.'/mod_absences/professeurs/prof_ajout_abs.php'.$groupe_abs.'">Absences</a></li>';
		if (getSettingValue("active_module_absence")=='y' ) {
		    $tbs_menu_prof[]=array("lien"=>'/mod_absences/professeurs/prof_ajout_abs.php'.$groupe_abs , "texte"=>"Absences");
		} else if (getSettingValue("active_module_absence")=='2' ) {
		    $tbs_menu_prof[]=array("lien"=>'/mod_abs2/index.php'.$groupe_abs , "texte"=>"Absences");
		}
		
	}else{$barre_absence = '';}

	// Module Cahier de textes
	if (getSettingValue("active_cahiers_texte") == 'y') {
		//$barre_textes = '<li><a href="'.$gepiPath.'/cahier_texte/index.php'.$groupe_text.'">C. de Textes</a></li>';
		$tbs_menu_prof[]=array("lien"=>'/cahier_texte/index.php'.$groupe_text , "texte"=>"C. de Textes");
	}else{$barre_textes = '';}

	// Module carnet de notes
	if(getSettingValue("active_carnets_notes") == 'y'){
		//$barre_note = '<li><a href="'.$gepiPath.'/cahier_notes/index.php">Notes</a></li>
		//<li><a href="'.$gepiPath.'/saisie/index.php">Bulletins</a></li>';		
		$tbs_menu_prof[]=array("lien"=> '/cahier_notes/index.php' , "texte"=>"Notes");
		$tbs_menu_prof[]=array("lien"=> '/saisie/index.php' , "texte"=>"Bulletins");
	}else{$barre_note = '';}

	// Module emploi du temps
	if (getSettingValue("autorise_edt_tous") == "y") {
		//$barre_edt = '<li><a href="'.$gepiPath.'/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof">Emploi du tps</a></li>';
		$tbs_menu_prof[]=array("lien"=> '/edt_organisation/index_edt.php?visioedt=prof1&amp;login_edt='.$_SESSION["login"].'&amp;type_edt_2=prof' , "texte"=>"Emploi du tps");
	}else{$barre_edt = '';}

	// Module discipline
	if (getSettingValue("active_mod_discipline")=='y') {
	    //$barre_discipline = "<li><a href=".$gepiPath."/mod_discipline/index.php>Discipline</a></li>";
		$tbs_menu_prof[]=array("lien"=> '/mod_discipline/index.php' , "texte"=>"Discipline");
	} else {$barre_discipline = '';}

	// Module notanet
	if (getSettingValue("active_notanet") == "y") {
		//$barre_notanet = '<li><a href="'.$gepiPath.'/mod_notanet/index.php">Brevet</a></li>';
		$tbs_menu_prof[]=array("lien"=> '/mod_notanet/index.php' , "texte"=>"Brevet");
	}else{ $barre_notanet = '';}

	/*
	function acces($id,$statut) {
		$tab_id = explode("?",$id);
		$query_droits = @mysql_query("SELECT * FROM droits WHERE id='$tab_id[0]'");
		$droit = @mysql_result($query_droits, 0, $statut);
		if ($droit == "V") {
			return "1";
		} else {
			return "0";
		}
	}
	*/

	if (acces('/eleves/visu_eleve.php',$_SESSION['statut'])==1) {
		//$barre_consult_eleve = '<li><a href="'.$gepiPath.'/eleves/visu_eleve.php">Consult.élève</a></li>';
		$tbs_menu_prof[]=array("lien"=> '/eleves/visu_eleve.php' , "texte"=>"Consult.élève");
	}
	else{ $barre_consult_eleve = '';}

/*
	echo '
	<ol id="essaiMenu">
		<li><a href="'.$gepiPath.'/accueil.php">Accueil</a></li>
		'.$barre_absence.'
		'.$barre_textes.'
		'.$barre_note.'
		'.$barre_edt.'
		'.$barre_discipline.'
		'.$barre_notanet.'
		<li><a href="'.$gepiPath.'/utilisateurs/mon_compte.php">Mon compte</a></li>
	</ol>
	';
*/
?>
